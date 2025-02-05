<?php
class HC_Security {
    public function __construct() {
        add_filter('wp_authenticate_user', array($this, 'check_user_status'), 10, 2);
        add_action('user_register', array($this, 'add_user_verification_meta'));
        add_filter('login_errors', array($this, 'custom_login_errors'));
    }

    public function check_user_status($user, $password) {
        if (!is_wp_error($user)) {
            $is_verified = get_user_meta($user->ID, 'hc_user_verified', true);
            
            if (!$is_verified) {
                return new WP_Error('user_not_verified', 'حساب کاربری شما هنوز تأیید نشده است.');
            }
        }
        return $user;
    }

    public function add_user_verification_meta($user_id) {
        $verification_required = get_option('hc_enable_user_verification', true);
        
        if ($verification_required) {
            update_user_meta($user_id, 'hc_user_verified', false);
            $this->send_verification_email($user_id);
        } else {
            update_user_meta($user_id, 'hc_user_verified', true);
        }
    }

    private function send_verification_email($user_id) {
        $user = get_userdata($user_id);
        $verification_code = wp_generate_password(20, false);
        
        update_user_meta($user_id, 'hc_verification_code', $verification_code);
        
        $verification_link = add_query_arg([
            'action' => 'verify_user',
            'user_id' => $user_id,
            'code' => $verification_code
        ], wp_login_url());

        $subject = 'تأیید حساب کاربری';
        $message = "برای تأیید حساب کاربری، روی لینک زیر کلیک کنید:\n\n{$verification_link}";
        
        wp_mail($user->user_email, $subject, $message);
    }

    public function custom_login_errors($error) {
        global $errors;
        $error_codes = $errors->get_error_codes();
        
        if (in_array('invalid_username', $error_codes) || 
            in_array('incorrect_password', $error_codes)) {
            $error = 'نام کاربری یا رمز عبور اشتباه است.';
        }
        
        return $error;
    }
}
