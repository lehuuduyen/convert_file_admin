<?php
/**
 * CSS - Javascript for admin
 */
add_action('admin_enqueue_scripts', 'styleAbout');
function styleAbout()
{
    if (is_admin()) {
        wp_enqueue_style('style-About', get_template_directory_uri() . '/assets/css/about.css');
        wp_enqueue_style('style-About');
        wp_enqueue_script('script-About', get_template_directory_uri() . '/assets/js/about.js');
        wp_enqueue_media();
    }
}

?>
