<?php
add_action('cmb2_init', 'postTemplateService');
function postTemplateService() 
{
    $metaBox = new_cmb2_box([
        'id' => KEY_TEMPLATE_SERVICE . '_box',
        'title' => 'Template service',
        'object_types' => POST_TYPE,
        'context' => 'normal',
        'priority' => 'default',
        'show_names' => true,
        
    ]);
    $metaBox->add_field( array(
        'name' => 'Template service',
        'id' => KEY_TEMPLATE_SERVICE . '_id',
        'type' => 'select',
        'default'          => 'template1',
        'options'          => array(
            '1' => __( 'Teamplate one', 'cmb2' ),
            '2'   => __( 'Teamplate Two', 'cmb2' ),
            '3'     => __( 'Teamplate Three', 'cmb2' ),
        ),
    ));
    
}

