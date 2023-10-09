<?php
add_action('cmb2_init', 'pageAboutMissionBusiness');
function pageAboutMissionBusiness()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_ABOUT_MISSION_BUSINESS . '_box',
        'title' => "Mission Business",
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getAboutPageId())),
    ));
    $metaBox->add_field( array(
        'name' => 'Title',
        'id' => KEY_ABOUT_MISSION_BUSINESS . '_title',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control',
            'data-cmb2-qtranslate' => true,

        ),
    ));
    $metaBoxGroup = $metaBox->add_field(array(
        'id' => KEY_ABOUT_MISSION_BUSINESS . '_group',
        'type' => 'group',
        'options' => array(
            'group_title' => 'Group {#}',
            'add_button' => 'ADD',
            'remove_button' => 'REMOVE',
            //Confilct when use repeater in group, fix later
            'sortable' => true,
            'closed' => false,
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Title',
        'id' => KEY_ABOUT_MISSION_BUSINESS . '_group_title',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control',
            'data-cmb2-qtranslate' => true,
        ),
    ));

    
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Summary',
        'id' => KEY_ABOUT_MISSION_BUSINESS . '_group_summary',
        'type' => 'wysiwyg',
       
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
    ));
    
    
}
