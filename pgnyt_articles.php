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

function pgnyt_articles_menu()
{
    add_options_page(
        "NY Times Plugin",
        "NY Times Articles",
        "manage_options",
        "pgnyt-articles",
        "pgnyt_articles_options_page",
    );
}

add_action("admin_menu", "pgnyt_articles_menu");

function pgnyt_articles_options_page()
{
    if (!current_user_can("manage_options")) {
        wp_die('You don\'t have enough permission to view this page');
    }

    global $plugin_url;
    global $options;

    if (isset($_POST['pgnyt_form_submitted'])) {
        $hidden_field = esc_html($_POST['pgnyt_form_submitted']);

        if ($hidden_field == 'Y') {
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

    $options = get_option('pgnyt_articles');

    if ($options != '') {
        $pgnyt_search = $options['pgnyt_search'];
        $pgnyt_apikey = $options['pgnyt_apikey'];
        $pgnyt_results = $options['pgnyt_results'];
    }

    require('inc/options-page-wrapper.php');
}

class Pgnyt_Articles_Widget extends WP_Widget
{
    public function __construct()
    {
        // actual widget processes
        parent::__construct(
            'pgnyt_articles_widget', // Base ID
            'NY Times Articles Widget', // Name
            array('description' => 'Displays NY Times articles') // Args
        );
    }

    public function widget($args, $instance)
    {
        // outputs the content of the widget
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $num_articles = $instance['num_articles'];
        $display_image = $instance['display_image'];

        $options = get_option('pgnyt_articles');
        $pgnyt_results = $options['pgnyt_results'];

        require('inc/front-end.php');
    }

    public function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['display_image'] = strip_tags($new_instance['display_image']);
        $instance['num_articles'] = strip_tags($new_instance['num_articles']);

        return $instance;
    }

    public function form($instance)
    {
        // outputs the options form in the admin
        $title = esc_attr($instance['title']);
        $display_image = esc_attr($instance['display_image']);
        $num_articles = esc_attr($instance['num_articles']);

        $options = get_option('pgnyt_articles');
        $pgnyt_results = $options['pgnyt_results'];

        require('inc/widget-fields.php');

    }
}

add_action("widgets_init", "wpdocs_register_widgets");

function wpdocs_register_widgets()
{
    register_widget('Pgnyt_Articles_Widget');
}

function pgnyt_articles_shortcode($atts, $content = null)
{
    global $post;

    extract(shortcode_atts(array(
        'num_articles' => 5,
        'display_image' => 'on',
        'title' => 'NYT Articles'
    ), $atts));

    if ($display_image == 'on')
        $display_image = 1;
    if ($display_image == 'off')
        $display_image = 0;

    $options = get_option('pgnyt_articles');
    $pgnyt_results = $options['pgnyt_results'];

    // Set the variables that front-end.php expects
    $before_widget = '<div class="pgnyt-widget">';
    $after_widget = '</div>';
    $before_title = '<h3 class="pgnyt-title">';
    $after_title = '</h3>';
    
    // Use the title from shortcode attributes or default
    $title = !empty($atts['title']) ? $atts['title'] : 'NYT Articles';

    ob_start();

    require('inc/front-end.php');

    $content = ob_get_clean();

    return $content;
}

add_shortcode( 'pgnyt_articles', 'pgnyt_articles_shortcode' );

function pgnyt_articles_get_results($pgnyt_search, $pgnyt_apikey)
{
    $json_feed_url = "https://api.nytimes.com/svc/search/v2/articlesearch.json?q=" . $pgnyt_search . "&api-key=" . $pgnyt_apikey;

    $json_feed = wp_remote_get($json_feed_url);

    $pgnyt_results = json_decode($json_feed['body']);

    return $pgnyt_results;
}

function pgnyt_articles_refresh_results() {
    $options = get_option('pgnyt_articles');
    $last_updated = $options['last_updated'];

    $current_time = time();
    $update_difference = $current_time - $last_updated;

    if($update_difference > 86400) {
        $pgnyt_search = $options['pgnyt_search'];
        $pgnyt_apikey = $options['pgnyt_apikey'];

        $options['pgnyt_results'] = pgnyt_articles_get_results($pgnyt_search, $pgnyt_apikey);
        $options['last_updated'] = time();

        update_option('pgnyt_articles', $options);
    }

    die();

}

add_action( 'wp_ajax_pgnyt_articles_refresh_results', 'pgnyt_articles_refresh_results' );

function pgnyt_articles_enable_frontend_ajax() {
    ?>
        <script>
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
    <?php
}

add_action('wp_head', 'pgnyt_articles_enable_frontend_ajax');

function pgnyt_articles_backend_styles()
{
    wp_enqueue_style("pgnyt_articles_backend_css", plugins_url('pgnyt_articles/pgnyt-articles.css'));
}

add_action('admin_head', "pgnyt_articles_backend_styles");

function pgnyt_articles_frontend_styles(){
	wp_enqueue_style( 'pgnyt_articles_frontend_css', plugins_url( 'pgnyt_articles/pgnyt-articles.css' ) );
    wp_enqueue_script('pgnyt_articles_frontend_js', plugins_url( 'pgnyt_articles/pgnyt-articles.js'), array('jquery'), '', true );

}

add_action('wp_enqueue_scripts', 'pgnyt_articles_frontend_styles');

?>