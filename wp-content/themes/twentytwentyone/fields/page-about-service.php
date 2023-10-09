<?php
add_action('cmb2_init', 'pageAboutService');
function pageAboutService()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_ABOUT_SERVICE . '_box',
        'title' => 'Service',
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getAboutPageId())),
    ));
    $metaBox->add_field( array(
        'name' => 'Title',
        'id' => KEY_ABOUT_SERVICE . '_title',
        'type' => 'text',
        'attributes' => array(
            'data-cmb2-qtranslate' => true,
            'class' => 'form-control'
        )
    ));
    $metaBox->add_field( array(
        'name'    => 'Thumbnail about',
        // 'desc'    => 'Upload an image or enter an URL.',
        'id'      => KEY_ABOUT_SERVICE . '_thumbnail',
        'type'    => 'file',
        // Optional:
        'options' => array(
            'url' => false, // Hide the text input for the url
        ),
        'text'    => array(
            'add_upload_file_text' => 'Add File' // Change upload button text. Default: "Add or Upload File"
        ),
        // query_args are passed to wp.media's library query.
        'query_args' => array(
            // 'type' => 'application/pdf', // Make library only display PDFs.
            // Or only allow gif, jpg, or png images
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'large', // Image size to use when previewing in the admin.
    ) );
    $metaBox->add_field(array(
        'name' => 'Attached Posts',
        'id' => KEY_ABOUT_SERVICE . '_post',
        'desc' => '表示枠は最大８とし、デフォルトは表示枠１のものを表示する。',
        'type' => 'custom_attached_posts',
        'column' => true,
        'options' => array(
            'show_thumbnails' => true,
            'filter_boxes' => true,
            'query_args' => array(
                'posts_per_page' => 10,
                'post_type' => 'post',
                'post_status' => ['publish'],
                'cat' => get_cat_ID("service"),
                'meta_compare' => 'EXISTS',
            ),
        ),
    ));
}
?>