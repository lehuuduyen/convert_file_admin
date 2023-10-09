<?php
add_action('cmb2_init', 'postSummary');
function postSummary() 
{
    $metaBox = new_cmb2_box([
        'id' => KEY_SUMMARY . '_box',
        'title' => 'Mô tả',
        'object_types' => POST_TYPE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        
    ]);
    $metaBox->add_field(array(
        'name' => 'Mô tả ngắn',
        'id' => KEY_SUMMARY ,
        'type' => 'wysiwyg',
        'attributes' => array(
            'class' => 'form-control'
        ),
        'options' => array(
            'editor_class' => 'cmb2-qtranslate'
          )
    ));
}

