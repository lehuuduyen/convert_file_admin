<?php
add_action('cmb2_init', 'pageAboutTechnologyInDochina');
function pageAboutTechnologyInDochina()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_ABOUT_TECHNOLOGY_IN_DOCHINA . '_box',
        'title' => "Technology in dochina",
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getAboutPageId())),
    ));
    $metaBox->add_field( array(
        'name' => 'Title',
        'id' => KEY_ABOUT_TECHNOLOGY_IN_DOCHINA . '_title',
        'type' => 'text',
        'attributes' => array(
            'data-cmb2-qtranslate' => true,
            'class' => 'form-control'
        ),
    ));
    
    $metaBox->add_field( array(
        'name'    => 'Image',
        // 'desc'    => 'Upload an image or enter an URL.',
        'id'      => KEY_ABOUT_TECHNOLOGY_IN_DOCHINA . '_image',
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

   
    
}
