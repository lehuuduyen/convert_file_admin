<?php
add_action('cmb2_init', 'pagePartnerSlide');
function pagePartnerSlide()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_PARTNER_SLIDE . '_box',
        'title' => 'Partner',
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getTopPageId())),
    ));
    $metaBox->add_field( array(
        'name' => 'Title',
        'id' => KEY_PARTNER_SLIDE . '_title',
        'type' => 'text',
        'attributes' => array(
            'data-cmb2-qtranslate' => true,
            'class' => 'form-control'
        )
    ));
    $metaBox->add_field( array(
        'name' => 'List Partner',
        'desc' => '',
        'id'   => KEY_PARTNER_SLIDE . '_list',
        'type' => 'file_list',
        'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
        // 'query_args' => array( 'type' => 'image' ), // Only images attachment
        // Optional, override default text strings
        'text' => array(
            'add_upload_files_text' => 'Replacement', // default: "Add or Upload Files"
            'remove_image_text' => 'Replacement', // default: "Remove Image"
            'file_text' => 'Replacement', // default: "File:"
            'file_download_text' => 'Replacement', // default: "Download"
            'remove_text' => 'Replacement', // default: "Remove"
        ),
    ) );
    
}
?>