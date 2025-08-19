<?php 

    /**
     * Plugin Name: WP NY Times Plugin 
     * Plugin URI:  https://wordpress.org/plugins/pgnyt_articles/
     * Description: Provides both widgets and shortcodes to help you display NY Times articles on your website.
     * Version:     1.0
     * Author:      Dolon Chandra Roy
     * License:     GPLv2 or later
     */

    $plugin_url = WP_PLUGIN_URL . '/pgnyt-articles';
    $options = array();

    function pgnyt_articles_menu() {
        add_options_page(
            "NY Times Plugin",
            "NY Times Articles",
            "manage_options",
            "pgnyt-articles",
            "pgnyt_articles_options_page",
        );
    }

    add_action("admin_menu", "pgnyt_articles_menu");

    function pgnyt_articles_options_page() {
        if(!current_user_can("manage_options")) {
            wp_die('You don\'t have enough permission to view this page');
        }

        global $plugin_url;
        global $options;

        if(isset($_POST['pgnyt_form_submitted'])) {
            $hidden_field = esc_html($_POST['pgnyt_form_submitted']);

            if($hidden_field == 'Y') {
                $pgnyt_search = esc_html($_POST['pgnyt_search']);
                $pgnyt_apikey = esc_html($_POST['pgnyt_apikey']);

                $pgnyt_results = pgnyt_articles_get_results($pgnyt_search, $pgnyt_apikey);

                $options['pgnyt_search'] = $pgnyt_search; 
                $options['pgnyt_apikey'] = $pgnyt_apikey;
                $options['last_updated'] = time();

                $options['pgnyt_results'] = $pgnyt_results;
            
                update_option('pgnyt_articles', $options);
            }
        }

        $options = get_option( 'pgnyt_articles');

        if($options != '') {
            $pgnyt_search = $options['pgnyt_search'];
            $pgnyt_apikey = $options['pgnyt_apikey'];
            $pgnyt_results = $options['pgnyt_results'];
        }

        require('inc/options-page-wrapper.php');
    }

    function pgnyt_articles_get_results($pgnyt_search, $pgnyt_apikey) {
        $json_feed_url = "https://api.nytimes.com/svc/search/v2/articlesearch.json?q=" . $pgnyt_search . "&api-key=" . $pgnyt_apikey;

        $json_feed = wp_remote_get($json_feed_url);

        $pgnyt_results = json_decode($json_feed['body']);

        return $pgnyt_results;
    }

    function pgnyt_articles_backend_styles() {
        wp_enqueue_style("pgnyt_articles_backend_css", plugins_url( 'pgnyt_articles/pgnyt-articles.css' ));
    }

    add_action( 'admin_head', "pgnyt_articles_backend_styles");

?>