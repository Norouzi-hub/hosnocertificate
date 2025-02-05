<?php
class HC_Business_Manager {
    public function __construct() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        // Load any additional dependencies here
    }

    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'hosnocertificate',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    private function define_admin_hooks() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    private function define_public_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style('hc-admin-style', HC_PLUGIN_URL . 'admin/css/hc-admin.css', array(), HC_VERSION, 'all');
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('hc-admin-script', HC_PLUGIN_URL . 'admin/js/hc-admin.js', array('jquery'), HC_VERSION, false);
    }

    public function enqueue_public_styles() {
        wp_enqueue_style('hc-public-style', HC_PLUGIN_URL . 'public/css/hc-style.css', array(), HC_VERSION, 'all');
    }

    public function enqueue_public_scripts() {
        wp_enqueue_script('hc-public-script', HC_PLUGIN_URL . 'public/js/hc-public.js', array('jquery'), HC_VERSION, false);
    }

    public function run() {
        // Run the plugin
    }
}
