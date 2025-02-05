<?php
class HC_Search {
    public function __construct() {
        add_action('wp_ajax_hc_search_businesses', array($this, 'ajax_search_businesses'));
        add_action('wp_ajax_nopriv_hc_search_businesses', array($this, 'ajax_search_businesses'));
    }

    public function ajax_search_businesses() {
        check_ajax_referer('hc_search_nonce', 'nonce');

        $search_term = sanitize_text_field($_POST['search_term']);
        $province = sanitize_text_field($_POST['province']);
        $city = sanitize_text_field($_POST['city']);
        $category = sanitize_text_field($_POST['category']);
        $min_rating = floatval($_POST['min_rating']);

        $args = array(
            'post_type' => 'hc_business',
            'posts_per_page' => -1,
            'meta_query' => array(),
            'tax_query' => array()
        );

        if (!empty($search_term)) {
            $args['s'] = $search_term;
        }

        if (!empty($province)) {
            $args['meta_query'][] = array(
                'key' => 'hc_province',
                'value' => $province,
                'compare' => '='
            );
        }

        if (!empty($city)) {
            $args['meta_query'][] = array(
                'key' => 'hc_city',
                'value' => $city,
                'compare' => '='
            );
        }

        if (!empty($category)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'hc_business_category',
                'field' => 'slug',
                'terms' => $category
            );
        }

        if ($min_rating > 0) {
            $args['meta_query'][] = array(
                'key' => 'hc_average_rating',
                'value' => $min_rating,
                'compare' => '>=',
                'type' => 'NUMERIC'
            );
        }

        $query = new WP_Query($args);
        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'rating' => get_post_meta(get_the_ID(), 'hc_average_rating', true),
                    'excerpt' => get_the_excerpt()
                );
            }
        }

        wp_reset_postdata();
        wp_send_json_success($results);
    }
}
