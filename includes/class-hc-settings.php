<?php
class HC_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_options_page(
            'تنظیمات هوشنو سرتیفیکیت',
            'هوشنو سرتیفیکیت',
            'manage_options',
            'hosno-certificate-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>تنظیمات هوشنو سرتیفیکیت</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('hosno_certificate_settings');
                do_settings_sections('hosno-certificate-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('hosno_certificate_settings', 'hc_max_businesses_per_user');
        register_setting('hosno_certificate_settings', 'hc_enable_business_verification');
        register_setting('hosno_certificate_settings', 'hc_rating_weight_settings');

        add_settings_section(
            'hc_general_settings',
            'تنظیمات عمومی',
            null,
            'hosno-certificate-settings'
        );

        add_settings_field(
            'hc_max_businesses',
            'حداکثر تعداد کسب و کار برای هر کاربر',
            array($this, 'render_max_businesses_field'),
            'hosno-certificate-settings',
            'hc_general_settings'
        );

        add_settings_field(
            'hc_business_verification',
            'فعال‌سازی تأیید کسب و کارها',
            array($this, 'render_verification_field'),
            'hosno-certificate-settings',
            'hc_general_settings'
        );
    }

    public function render_max_businesses_field() {
        $value = get_option('hc_max_businesses_per_user', 5);
        echo "<input type='number' name='hc_max_businesses_per_user' value='{$value}' min='1' max='20' />";
    }

    public function render_verification_field() {
        $value = get_option('hc_enable_business_verification', 0);
        echo "<input type='checkbox' name='hc_enable_business_verification' value='1' " . checked(1, $value, false) . " />";
    }
}
