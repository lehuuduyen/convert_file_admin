<?php

function register_option_theme_page()
{
    add_menu_page('About', 'About', 'edit_pages', '/post.php?post=' . getAboutPageId() . '&action=edit', null, 'dashicons-list-view', null);

    add_menu_page('Top page', 'Top page', 'edit_pages', '/post.php?post=' . getTopPageId() . '&action=edit', null, 'dashicons-list-view', null);

}

add_action('admin_menu', 'register_option_theme_page');

