<?php
class HC_Roles {
    public function __construct() {
        add_action('init', array($this, 'create_custom_roles'));
        add_action('init', array($this, 'add_role_capabilities'));
    }

    public function create_custom_roles() {
        // نقش مدیر کسب و کار
        add_role('business_manager', 'مدیر کسب و کار', array(
            'read' => true,
            'edit_posts' => true,
            'edit_published_posts' => true,
            'upload_files' => true,
            'delete_posts' => true
        ));

        // نقش کارشناس
        add_role('expert', 'کارشناس', array(
            'read' => true,
            'edit_posts' => false,
            'upload_files' => false
        ));

        // نقش بازرس
        add_role('inspector', 'بازرس', array(
            'read' => true,
            'edit_posts' => false,
            'manage_options' => false
        ));
    }

    public function add_role_capabilities() {
        $roles = array('administrator', 'business_manager');
        
        foreach ($roles as $role_name) {
            $role = get_role($role_name);
            
            if ($role) {
                $role->add_cap('manage_businesses', true);
                $role->add_cap('edit_business', true);
                $role->add_cap('delete_business', true);
                $role->add_cap('publish_businesses', true);
            }
        }
    }

    public static function get_user_business_count($user_id) {
        $args = array(
            'post_type' => 'hc_business',
            'author' => $user_id,
            'posts_per_page' => -1
        );
        $businesses = get_posts($args);
        return count($businesses);
    }

    public static function can_add_business($user_id) {
        $max_businesses = get_option('hc_max_businesses_per_user', 5);
        $current_count = self::get_user_business_count($user_id);
        
        return $current_count < $max_businesses;
    }
}
