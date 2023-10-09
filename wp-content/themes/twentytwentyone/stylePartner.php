<?php
/**
 * CSS - Javascript for admin
 */
add_action('admin_enqueue_scripts', 'stylePartner');
function stylePartner()
{
    if (is_admin()) {
        wp_enqueue_style('style-Partner', get_template_directory_uri() . '/assets/css/partner.css');
        wp_enqueue_style('style-Partner');
        wp_enqueue_script('script-Partner', get_template_directory_uri() . '/assets/js/partner.js');
        wp_enqueue_media();
    }
}

?>
