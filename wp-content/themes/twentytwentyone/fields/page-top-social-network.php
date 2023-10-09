<?php
add_action('cmb2_init', 'pageSocicalNetworkGroup');
function pageSocicalNetworkGroup()
{
    $metaBox = new_cmb2_box(array(
        'id' => KEY_TOP_SOCICAL_NETWORK . '_box',
        'title' => "Socical network",
        'object_types' => POST_TYPE_PAGE,
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true,
        'show_on' => array('key' => 'id', 'value' => array(getTopPageId())),
    ));
   
    $metaBoxGroup = $metaBox->add_field(array(
        'id' => KEY_TOP_SOCICAL_NETWORK . '_group',
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
        'name' => 'Class icon',
        'id' => KEY_TOP_SOCICAL_NETWORK . '_icon',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control'
        ),
    ));
    $metaBox->add_group_field($metaBoxGroup, array(
        'name' => 'Link',
        'id' => KEY_TOP_SOCICAL_NETWORK . '_link',
        'type' => 'text',
        'attributes' => array(
            'class' => 'form-control'
        ),
    ));
    
    
}
