<?php
class HC_Activator {
    public static function activate() {
        // ایجاد جداول اختصاصی
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // جدول امتیازها
        $ratings_table = $wpdb->prefix . 'hc_ratings';
        $ratings_sql = "CREATE TABLE $ratings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            business_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            rating float NOT NULL,
            product_quality float,
            service_quality float,
            customer_service float,
            price_satisfaction float,
            accessibility float,
            comment text,
            status enum('pending','approved','rejected') DEFAULT 'pending',
            date_created datetime DEFAULT CURRENT_TIMESTAMP,
            date_updated datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // جدول رزروها
        $bookings_table = $wpdb->prefix . 'hc_bookings';
        $bookings_sql = "CREATE TABLE $bookings_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            business_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            service_id bigint(20) NOT NULL,
            booking_date date NOT NULL,
            booking_time time NOT NULL,
            status enum('pending','confirmed','cancelled') DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($ratings_sql);
        dbDelta($bookings_sql);

        // ایجاد نقش‌های کاربری
        $roles = new HC_Roles();
        $roles->create_custom_roles();
        $roles->add_role_capabilities();

        // تنظیمات پیش‌فرض
        add_option('hc_max_businesses_per_user', 5);
        add_option('hc_enable_business_verification', 1);
        add_option('hc_rating_weight_settings', [
            'product_quality' => 0.25,
            'service_quality' => 0.25,
            'customer_service' => 0.2,
            'price_satisfaction' => 0.15,
            'accessibility' => 0.15
        ]);

        // ایجاد صفحات اختصاصی
        $pages = [
            'user-dashboard' => [
                'title' => 'داشبورد کاربری',
                'content' => '[hc_user_dashboard]'
            ],
            'business-comparison' => [
                'title' => 'مقایسه کسب و کارها',
                'content' => '[hc_compare_businesses]'
            ]
        ];

        foreach ($pages as $slug => $page_data) {
            $page = get_page_by_path($slug);
            if (!$page) {
                wp_insert_post([
                    'post_title' => $page_data['title'],
                    'post_content' => $page_data['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $slug
                ]);
            }
        }
    }
}
