<?php
add_action( 'admin_init', 'page_about_register_settings' );
/* 
 * Register settings 
 */
function page_about_register_settings() 
{
    register_setting( 
        'general', 
        'about_id',
        'esc_html' // <--- Customize this if there are multiple fields
    );
    add_settings_section( 
        'site-guide', 
        '', 
        '__return_false', 
        'general' 
    );
    add_settings_field( 
        'about_id', 
        'Page about ID', 
        'page_about_print_text_editor', 
        'general', 
        'site-guide' 
    );
}    
/* 
 * Print settings field content 
 */
function page_about_print_text_editor($args) 
{
	$value = get_option( 'about_id', '' );
    echo '<input type="text" id="about_id" name="about_id" value="'.$value.'"' ;
}