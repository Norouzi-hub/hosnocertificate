<?php
class HC_Verification {
    private $verification_levels = [
        'basic' => [
            'price' => 0,
            'features' => ['basic_profile', 'standard_listing']
        ],
        'silver' => [
            'price' => 100000, // 10 هزار تومان
            'features' => [
                'verified_badge', 
                'priority_listing', 
                'detailed_profile',
                'monthly_analytics'
            ]
        ],
        'gold' => [
            'price' => 500000, // 50 هزار تومان
            'features' => [
                'verified_badge', 
                'top_listing', 
                'comprehensive_profile',
                'quarterly_analytics',
                'customer_support'
            ]
        ]
    ];

    public function __construct() {
        add_action('wp_ajax_hc_request_verification', [$this, 'request_verification']);
        add_action('admin_menu', [$this, 'add_verification_menu']);
    }

    public function request_verification() {
        $business_id = intval($_POST['business_id']);
        $level = sanitize_text_field($_POST['verification_level']);

        if (!isset($this->verification_levels[$level])) {
            wp_send_json_error('سطح اعتبارسنجی نامعتبر');
        }

        $verification_data = [
            'business_id' => $business_id,
            'level' => $level,
            'price' => $this->verification_levels[$level]['price'],
            'status' => 'pending_payment',
            'requested_at' => current_time('mysql')
        ];

        // ذخیره درخواست
        update_post_meta($business_id, 'hc_verification_request', $verification_data);

        wp_send_json_success([
            'message' => 'درخواست شما ثبت شد',
            'price' => $verification_data['price']
        ]);
    }

    public function add_verification_menu() {
        add_menu_page(
            'اعتبارسنجی کسب و کارها',
            'اعتبارسنجی',
            'manage_options',
            'hc-verification',
            [$this, 'render_verification_page'],
            'dashicons-shield',
            30
        );
    }

    public function render_verification_page() {
        $verified_businesses = $this->get_verification_requests();
        include(plugin_dir_path(__FILE__) . '../admin/partials/verification-page.php');
    }

    private function get_verification_requests() {
        $args = [
            'post_type' => 'hc_business',
            'meta_key' => 'hc_verification_request',
            'posts_per_page' => -1
        ];
        return get_posts($args);
    }

    public function apply_verification_features($business_id, $level) {
        $features = $this->verification_levels[$level]['features'];
        
        foreach ($features as $feature) {
            update_post_meta($business_id, "hc_feature_{$feature}", true);
        }

        // به‌روزرسانی وضعیت تأیید
        update_post_meta($business_id, 'hc_verification_status', $level);
    }
}
