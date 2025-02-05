<?php
class HC_Comparison {
    public function __construct() {
        add_shortcode('hc_compare_businesses', array($this, 'comparison_shortcode'));
        add_action('wp_ajax_hc_get_business_details', array($this, 'ajax_get_business_details'));
        add_action('wp_ajax_nopriv_hc_get_business_details', array($this, 'ajax_get_business_details'));
    }

    public function comparison_shortcode($atts) {
        ob_start();
        include(plugin_dir_path(__FILE__) . '../public/partials/hc-business-comparison.php');
        return ob_get_clean();
    }

    public function ajax_get_business_details() {
        $business_id = intval($_POST['business_id']);
        
        $details = [
            'title' => get_the_title($business_id),
            'ratings' => [
                'product_quality' => get_post_meta($business_id, 'hc_product_quality', true),
                'service_quality' => get_post_meta($business_id, 'hc_service_quality', true),
                'customer_service' => get_post_meta($business_id, 'hc_customer_service', true),
                'price_satisfaction' => get_post_meta($business_id, 'hc_price_satisfaction', true),
                'accessibility' => get_post_meta($business_id, 'hc_accessibility', true),
            ],
            'overall_rating' => get_post_meta($business_id, 'hc_average_rating', true),
            'certificate' => $this->get_certificate($business_id),
            'contact_info' => [
                'phone' => get_post_meta($business_id, 'hc_contact_phone', true),
                'mobile' => get_post_meta($business_id, 'hc_contact_mobile', true),
                'website' => get_post_meta($business_id, 'hc_website', true),
                'address' => get_post_meta($business_id, 'hc_address', true),
            ],
            'services' => get_post_meta($business_id, 'hc_services', true),
        ];

        wp_send_json_success($details);
    }

    private function get_certificate($business_id) {
        $overall_rating = get_post_meta($business_id, 'hc_average_rating', true);
        
        if ($overall_rating >= 9) {
            return 'گواهینامه طلایی';
        } elseif ($overall_rating >= 7) {
            return 'گواهینامه نقره‌ای';
        } elseif ($overall_rating >= 5) {
            return 'گواهینامه برنزی';
        } else {
            return 'بدون گواهینامه';
        }
    }

    public function get_comparable_businesses() {
        $args = [
            'post_type' => 'hc_business',
            'posts_per_page' => -1,
            'orderby' => 'meta_value_num',
            'meta_key' => 'hc_average_rating'
        ];

        return get_posts($args);
    }
}
