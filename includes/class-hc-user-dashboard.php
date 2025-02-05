<?php
class HC_User_Dashboard {
    public function __construct() {
        add_shortcode('hc_user_dashboard', [$this, 'render_dashboard']);
        add_action('wp_ajax_hc_dashboard_stats', [$this, 'get_dashboard_stats']);
    }

    public function render_dashboard() {
        if (!is_user_logged_in()) {
            return 'برای مشاهده داشبورد باید وارد شوید.';
        }

        ob_start();
        include(plugin_dir_path(__FILE__) . '../public/partials/user-dashboard.php');
        return ob_get_clean();
    }

    public function get_dashboard_stats() {
        $user_id = get_current_user_id();
        $stats = [
            'total_businesses' => $this->get_user_businesses_count($user_id),
            'total_ratings' => $this->get_user_ratings_count($user_id),
            'total_bookings' => $this->get_user_bookings_count($user_id),
            'recent_activities' => $this->get_recent_activities($user_id)
        ];

        wp_send_json_success($stats);
    }

    private function get_user_businesses_count($user_id) {
        $args = [
            'post_type' => 'hc_business',
            'author' => $user_id,
            'posts_per_page' => -1
        ];
        return count(get_posts($args));
    }

    private function get_user_ratings_count($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'hc_ratings';
        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
            $user_id
        ));
    }

    private function get_user_bookings_count($user_id) {
        $args = [
            'post_type' => 'hc_booking',
            'author' => $user_id,
            'posts_per_page' => -1
        ];
        return count(get_posts($args));
    }

    private function get_recent_activities($user_id) {
        $activities = [];

        // کسب و کارهای اخیر
        $businesses = get_posts([
            'post_type' => 'hc_business',
            'author' => $user_id,
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        foreach ($businesses as $business) {
            $activities[] = [
                'type' => 'business_created',
                'title' => $business->post_title,
                'date' => $business->post_date
            ];
        }

        // رزروهای اخیر
        $bookings = get_posts([
            'post_type' => 'hc_booking',
            'author' => $user_id,
            'posts_per_page' => 5,
            'orderby' => 'date',
            'order' => 'DESC'
        ]);

        foreach ($bookings as $booking) {
            $activities[] = [
                'type' => 'booking_created',
                'title' => get_post_meta($booking->ID, 'hc_booking_business', true),
                'date' => $booking->post_date
            ];
        }

        // مرتب‌سازی فعالیت‌ها بر اساس تاریخ
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 10);
    }
}
