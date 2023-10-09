<?php
add_action('cmb2_init', 'postListImages');
function postListImages()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_LIST_IMAGES . '_box',
        'title' => 'List Images',
        'object_types' => POST_TYPE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
    ));
    $metaBox->add_field( array(
        'name'    => 'Hình ảnh',
        // 'desc'    => 'Upload an image or enter an URL.',
        'id'      => KEY_LIST_IMAGES . '_icon',
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
    // $metaBox->add_field( array(
    //     'name' => 'List Images',
    //     'desc' => '',
    //     'id'   => KEY_LIST_IMAGES . '_list',
    //     'type' => 'file_list',
    //     'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
    //     // 'query_args' => array( 'type' => 'image' ), // Only images attachment
    //     // Optional, override default text strings
    //     'text' => array(
    //         'add_upload_files_text' => 'Replacement', // default: "Add or Upload Files"
    //         'remove_image_text' => 'Replacement', // default: "Remove Image"
    //         'file_text' => 'Replacement', // default: "File:"
    //         'file_download_text' => 'Replacement', // default: "Download"
    //         'remove_text' => 'Replacement', // default: "Remove"
    //     ),
    // ) );
}
?>