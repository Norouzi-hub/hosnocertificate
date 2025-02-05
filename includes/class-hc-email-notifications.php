<?php
class HC_Email_Notifications {
    private $from_email;
    private $from_name;

    public function __construct() {
        $this->from_email = get_option('admin_email');
        $this->from_name = get_bloginfo('name');

        add_action('hc_new_business_registered', array($this, 'send_business_registration_email'), 10, 1);
        add_action('hc_new_rating_submitted', array($this, 'send_rating_notification_email'), 10, 2);
        add_action('hc_business_badge_earned', array($this, 'send_badge_earned_email'), 10, 2);
    }

    private function send_email($to, $subject, $message) {
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            "From: {$this->from_name} <{$this->from_email}>"
        );

        return wp_mail($to, $subject, $message, $headers);
    }

    public function send_business_registration_email($business_id) {
        $business = get_post($business_id);
        $owner_email = get_the_author_meta('user_email', $business->post_author);
        
        $subject = 'ثبت کسب و کار جدید در ' . get_bloginfo('name');
        $message = sprintf(
            '<h2>کسب و کار شما با موفقیت ثبت شد</h2>
            <p>کسب و کار %s با موفقیت در سیستم ثبت گردید.</p>
            <p>لینک کسب و کار: %s</p>',
            $business->post_title,
            get_permalink($business_id)
        );

        $this->send_email($owner_email, $subject, $message);
    }

    public function send_rating_notification_email($business_id, $rating_data) {
        $business_owner_id = get_post_field('post_author', $business_id);
        $owner_email = get_the_author_meta('user_email', $business_owner_id);
        
        $subject = 'امتیاز جدید برای کسب و کار شما';
        $message = sprintf(
            '<h2>امتیاز جدید دریافت شد</h2>
            <p>کسب و کار %s یک امتیاز جدید دریافت کرده است.</p>
            <p>امتیاز: %s</p>
            <p>نظر: %s</p>',
            get_the_title($business_id),
            $rating_data['rating'],
            $rating_data['comment'] ?? 'بدون نظر'
        );

        $this->send_email($owner_email, $subject, $message);
    }

    public function send_badge_earned_email($business_id, $badge) {
        $business_owner_id = get_post_field('post_author', $business_id);
        $owner_email = get_the_author_meta('user_email', $business_owner_id);
        
        $subject = 'نشان جدید کسب شده';
        $message = sprintf(
            '<h2>تبریک! نشان جدیدی کسب کردید</h2>
            <p>کسب و کار %s نشان %s را دریافت کرد.</p>',
            get_the_title($business_id),
            $badge
        );

        $this->send_email($owner_email, $subject, $message);
    }
}
