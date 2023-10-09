<?php
add_action('cmb2_init', 'pageTopInformation');
function pageTopInformation()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_TOP_INFORMATION . '_box',
        'title' => 'Information',
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getTopPageId())),
    ));
   
    $metaBox->add_field(array(
        'name' => 'Title',
        'id' => KEY_TOP_INFORMATION . '_title',
        'type' => 'text',
        'attributes' => array(
            'data-cmb2-qtranslate' => true,
            'class' => 'form-control'
        )
    ));
    $metaBox->add_field(array(
        'name' => 'Slug',
        'id' => KEY_TOP_INFORMATION . '_slug',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control'
        )
    ));
    $metaBox->add_field(array(
        'name' => 'Short description',
        'id' => KEY_TOP_INFORMATION . '_short_description',
        'type' => 'wysiwyg',
        'attributes' => array(
            'class' => 'form-control'
        ),
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
    ));
    $metaBox->add_field( array(
        'name'    => 'Image',
        // 'desc'    => 'Upload an image or enter an URL.',
        'id'      => KEY_TOP_INFORMATION . '_image',
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
?>