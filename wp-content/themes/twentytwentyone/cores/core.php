<?php
/**
 * Hide toolbar
 * @return bool
 */
add_filter('show_admin_bar', 'hideAdminBar');
function hideAdminBar()
{
    return false;
}

/**
 * Go back editor old
 */
add_filter('use_block_editor_for_post', '__return_false');

/**
 * Disable auto image crop
 */
add_filter('image_resize_dimensions', 'czc_disable_crop', 10, 6);

function czc_disable_crop($enable, $orig_w, $orig_h, $dest_w, $dest_h, $crop)
{
    // Instantly disable this filter after the first run
    // remove_filter( current_filter(), __FUNCTION__ );
    // return image_resize_dimensions( $orig_w, $orig_h, $dest_w, $dest_h, false );
    return false;
}

add_action('init', 'czc_disable_extra_image_sizes');
function czc_disable_extra_image_sizes()
{
    foreach (get_intermediate_image_sizes() as $size) {
        remove_image_size($size);
    }
}

add_theme_support('post-thumbnails');

/**
 * Menu
 */
if (function_exists('wp_nav_menu')) {
    function wp_my_menus()
    {
        register_nav_menus(array(
            'main' => __('Main Navigation'),
        ));
    }

    add_action('init', 'wp_my_menus');
}

/**
 * Change query default post type
 * Change query default taxonomy
 * @param $query
 */
if (is_admin()) {
    add_action('pre_get_posts', 'changeDefaultPostOrder', 99);
    function changeDefaultPostOrder($query)
    {
        $query->set('orderby', 'ID');
        $query->set('order', 'DESC');
    }

    add_filter('get_terms_orderby', 'changeDefaultTermOrder', 10, 2);
    function changeDefaultTermOrder($orderby, $args)
    {
        return 't.term_id';
    }
}

/**
 * Update btn preview link
 */
add_filter('preview_post_link', 'updatePreview');
function updatePreview($preview_link)
{
    $post = get_post(get_the_ID());
    if (
        !is_admin()
        OR 'post.php' != $GLOBALS['pagenow']
    )
        return $preview_link;

    $args = array(
        'p' => $post->ID
    , 'preview' => 'true'
    );

    return add_query_arg($args, get_option('home') . '/preview/');
}

/**
 * Get field from cmb2
 * @param $slug
 * @return mixed
 */
function getField($slug)
{
    return get_post_meta(get_the_ID(), $slug, true);
}


/**
 * @param $excerpt
 * @return string
 */
function getExcerpt($excerpt, $length=EXCERPT_LENGTH) {
    $excerpt = wp_strip_all_tags($excerpt, true);
    if (mb_strlen($excerpt) > $length) {    
        $excerpt = mb_substr($excerpt, 0, $length);
    }
    return $excerpt;
}

add_filter('the_excerpt', 'getExcerpt', 21);

/**
 * Add option random term
 */
add_action( 'parse_term_query', function($q)
{
    $orderBy = $q->query_vars['orderby'];
    if( $orderBy == 'rand' )
    {
        add_filter( 'get_terms_orderby', function()
        {
            return 'RAND()';
        });
    }
});

?>
