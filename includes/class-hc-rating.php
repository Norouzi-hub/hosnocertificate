<?php
class HC_Rating {
    public function __construct() {
        add_action('wp_ajax_hc_submit_rating', array($this, 'ajax_submit_rating'));
        add_action('wp_ajax_nopriv_hc_submit_rating', array($this, 'ajax_submit_rating'));
    }

    public function ajax_submit_rating() {
        check_ajax_referer('hc_rating_nonce', 'nonce');

        $business_id = intval($_POST['business_id']);
        $user_id = get_current_user_id();
        $ratings = array(
            'product_quality' => intval($_POST['product_quality']),
            'service_quality' => intval($_POST['service_quality']),
            'customer_service' => intval($_POST['customer_service']),
            'price_satisfaction' => intval($_POST['price_satisfaction']),
            'accessibility' => intval($_POST['accessibility'])
        );
        $comment = sanitize_textarea_field($_POST['comment']);

        $result = $this->add_rating($business_id, $user_id, $ratings, $comment);

        if ($result) {
            $new_average = $this->calculate_average_rating($business_id);
            $certificate = $this->get_certificate($new_average);
            wp_send_json_success(array(
                'message' => 'امتیاز شما با موفقیت ثبت شد.',
                'new_average' => $new_average,
                'certificate' => $certificate
            ));
        } else {
            wp_send_json_error('خطا در ثبت امتیاز. لطفاً دوباره تلاش کنید.');
        }
    }

    private function add_rating($business_id, $user_id, $ratings, $comment) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hc_ratings';

        $existing_rating = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE business_id = %d AND user_id = %d",
            $business_id, $user_id
        ));

        $overall_rating = array_sum($ratings) / count($ratings);

        if ($existing_rating) {
            $result = $wpdb->update(
                $table_name,
                array(
                    'rating' => $overall_rating,
                    'product_quality' => $ratings['product_quality'],
                    'service_quality' => $ratings['service_quality'],
                    'customer_service' => $ratings['customer_service'],
                    'price_satisfaction' => $ratings['price_satisfaction'],
                    'accessibility' => $ratings['accessibility'],
                    'comment' => $comment,
                    'date_updated' => current_time('mysql')
                ),
                array('id' => $existing_rating)
            );
        } else {
            $result = $wpdb->insert(
                $table_name,
                array(
                    'business_id' => $business_id,
                    'user_id' => $user_id,
                    'rating' => $overall_rating,
                    'product_quality' => $ratings['product_quality'],
                    'service_quality' => $ratings['service_quality'],
                    'customer_service' => $ratings['customer_service'],
                    'price_satisfaction' => $ratings['price_satisfaction'],
                    'accessibility' => $ratings['accessibility'],
                    'comment' => $comment,
                    'date_created' => current_time('mysql'),
                    'date_updated' => current_time('mysql')
                )
            );
        }

        return $result !== false;
    }

    private function calculate_average_rating($business_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hc_ratings';

        $averages = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                AVG(rating) as overall,
                AVG(product_quality) as product_quality,
                AVG(service_quality) as service_quality,
                AVG(customer_service) as customer_service,
                AVG(price_satisfaction) as price_satisfaction,
                AVG(accessibility) as accessibility
            FROM $table_name 
            WHERE business_id = %d",
            $business_id
        ));

        update_post_meta($business_id, 'hc_average_rating', round($averages->overall, 2));
        update_post_meta($business_id, 'hc_product_quality', round($averages->product_quality, 2));
        update_post_meta($business_id, 'hc_service_quality', round($averages->service_quality, 2));
        update_post_meta($business_id, 'hc_customer_service', round($averages->customer_service, 2));
        update_post_meta($business_id, 'hc_price_satisfaction', round($averages->price_satisfaction, 2));
        update_post_meta($business_id, 'hc_accessibility', round($averages->accessibility, 2));

        return round($averages->overall, 2);
    }

    private function get_certificate($average) {
        if ($average >= 9) {
            return 'گواهینامه طلایی';
        } elseif ($average >= 7) {
            return 'گواهینامه نقره‌ای';
        } elseif ($average >= 5) {
            return 'گواهینامه برنزی';
        } else {
            return 'بدون گواهینامه';
        }
    }
}
