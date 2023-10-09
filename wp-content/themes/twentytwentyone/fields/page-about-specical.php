<?php
add_action('cmb2_init', 'pageAboutSpecical');
function pageAboutSpecical()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_ABOUT_SPECICAL . '_box',
        'title' => "Specical",
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getAboutPageId())),
    ));
   
    $metaBoxGroup = $metaBox->add_field(array(
        'id' => KEY_ABOUT_SPECICAL . '_group',
        'type' => 'group',
        'options' => array(
            'group_title' => 'Specical {#}',
            'add_button' => 'ADD',
            'remove_button' => 'REMOVE',
            //Confilct when use repeater in group, fix later
            'sortable' => true,
            'closed' => false,
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Number',
        'id' => KEY_ABOUT_SPECICAL . '_number',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control',
            'type' => 'number',
            'pattern' => '\d*',
    

        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Title',
        'id' => KEY_ABOUT_SPECICAL . '_title',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control',
            'data-cmb2-qtranslate' => true,

        ),
    ));
    
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Summary',
        'id' => KEY_ABOUT_SPECICAL . '_summary',
        'type' => 'wysiwyg',
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Class icon',
        'id' => KEY_ABOUT_SPECICAL . '_icon',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control'
        ),
    ));
    

   
    
}
