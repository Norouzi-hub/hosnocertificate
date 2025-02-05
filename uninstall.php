<?php
// اگر مستقیم به این فایل دسترسی داشته باشند، خروج کند
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// حذف تنظیمات
delete_option('hc_max_businesses_per_user');
delete_option('hc_enable_business_verification');
delete_option('hc_rating_weight_settings');

// حذف جداول اختصاصی
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}hc_ratings");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}hc_bookings");

// حذف پست‌های مرتبط
$business_posts = get_posts([
    'post_type' => 'hc_business',
    'numberposts' => -1
]);

foreach ($business_posts as $post) {
    wp_delete_post($post->ID, true);
}
