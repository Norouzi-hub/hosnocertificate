<?php
class HC_Booking {
    public function __construct() {
        add_action('init', array($this, 'register_booking_post_type'));
        add_action('wp_ajax_hc_create_booking', array($this, 'ajax_create_booking'));
        add_action('wp_ajax_nopriv_hc_create_booking', array($this, 'ajax_create_booking'));
    }

    public function register_booking_post_type() {
        $args = array(
            'public' => false,
            'label'  => 'رزروها',
            'supports' => array('title', 'author'),
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => 'do_not_allow',
            ),
            'map_meta_cap' => true,
        );
        register_post_type('hc_booking', $args);
    }

    public function ajax_create_booking() {
        check_ajax_referer('hc_booking_nonce', 'nonce');

        $business_id = intval($_POST['business_id']);
        $service_id = intval($_POST['service_id']);
        $date = sanitize_text_field($_POST['date']);
        $time = sanitize_text_field($_POST['time']);
        $user_id = get_current_user_id();

        if (!$user_id) {
            wp_send_json_error('برای رزرو باید وارد سیستم شوید.');
        }

        $booking_id = wp_insert_post(array(
            'post_type' => 'hc_booking',
            'post_title' => 'رزرو برای ' . get_the_title($business_id),
            'post_status' => 'publish',
            'post_author' => $user_id
        ));

        if ($booking_id) {
            update_post_meta($booking_id, 'hc_booking_business', $business_id);
            update_post_meta($booking_id, 'hc_booking_service', $service_id);
            update_post_meta($booking_id, 'hc_booking_date', $date);
            update_post_meta($booking_id, 'hc_booking_time', $time);
            update_post_meta($booking_id, 'hc_booking_status', 'pending');

            // ارسال ایمیل به صاحب کسب و کار
            $this->send_booking_notification($booking_id);

            wp_send_json_success('رزرو شما با موفقیت ثبت شد. منتظر تأیید صاحب کسب و کار باشید.');
        } else {
            wp_send_json_error('خطا در ثبت رزرو. لطفاً دوباره تلاش کنید.');
        }
    }

    private function send_booking_notification($booking_id) {
        $business_id = get_post_meta($booking_id, 'hc_booking_business', true);
        $business_owner_id = get_post_field('post_author', $business_id);
        $owner_email = get_the_author_meta('user_email', $business_owner_id);

        $subject = 'رزرو جدید برای کسب و کار شما';
        $message = sprintf(
            'یک رزرو جدید برای کسب و کار شما ثبت شده است. شناسه رزرو: %s',
            $booking_id
        );

        wp_mail($owner_email, $subject, $message);
    }
}
