<?php
add_action( 'admin_init', 'page_top_register_settings' );
/* 
 * Register settings 
 */
function page_top_register_settings() 
{
    register_setting( 
        'general', 
        'top_id',
        'esc_html' // <--- Customize this if there are multiple fields
    );
    add_settings_section( 
        'site-guide', 
        '', 
        '__return_false', 
        'general' 
    );
    add_settings_field( 
        'top_id', 
        'Page top ID', 
        'page_top_print_text_editor', 
        'general', 
        'site-guide' 
    );
}    
/* 
 * Print settings field content 
 */
function page_top_print_text_editor($args) 
{
	$value = get_option( 'top_id', '' );
    echo '<input type="text" id="top_id" name="top_id" value="'.$value.'"' ;
}