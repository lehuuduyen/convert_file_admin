<?php
/**
 * CSS - Javascript for admin
 */
add_action('admin_enqueue_scripts', 'styleRecommended');
function styleRecommended()
{
    if (is_admin()) {
        wp_enqueue_style('style-recommended', get_template_directory_uri() . '/assets/css/top.css');
        wp_enqueue_style('style-recommended');
        wp_enqueue_script('script-recommended', get_template_directory_uri() . '/assets/js/top.js');
        wp_enqueue_media();
    }
}

?>
