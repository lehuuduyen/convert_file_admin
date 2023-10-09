<?php
add_action('cmb2_init', 'pageTopContactGroup');
function pageTopContactGroup()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_TOP_CONTACT . '_box',
        'title' => "Contact info",
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getTopPageId())),
    ));
    
    $metaBox->add_field(array(
        'name' => 'Country',
        'id' => KEY_TOP_CONTACT . '_country',
        'type' => 'wysiwyg',
        'attributes' => array(
            'class' => 'form-control'
        ),
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
    ));
    $metaBox->add_field(array(
        'name' => 'Work time',
        'id' => KEY_TOP_CONTACT . '_work_time',
        'type' => 'wysiwyg',
        'attributes' => array(
            'class' => 'form-control'
        ),
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
        
    ));
    $metaBoxGroup = $metaBox->add_field(array(
        'id' => KEY_TOP_CONTACT . '_group',
        'type' => 'group',
        'options' => array(
            'group_title' => 'Contact {#}',
            'add_button' => 'ADD',
            'remove_button' => 'REMOVE',
            //Confilct when use repeater in group, fix later
            'sortable' => true,
            'closed' => false,
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Title',
        'id' => KEY_TOP_CONTACT . '_title',
        'type' => 'text',
        'attributes' => array(
            'data-cmb2-qtranslate' => true,
            'class' => 'form-control'
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Mobile',
        'id' => KEY_TOP_CONTACT . '_mobile',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control'
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Email',
        'id' => KEY_TOP_CONTACT . '_email',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control'
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Address',
        'id' => KEY_TOP_CONTACT . '_address',
        'type' => 'wysiwyg',
    
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
    ));
    
}
