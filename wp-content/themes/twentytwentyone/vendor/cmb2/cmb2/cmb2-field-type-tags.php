<?php 
/*
Plugin Name: CMB Field Type: Tags
Plugin URI: https://github.com/florianbeck/cmb2-field-tags
Description: Tags field type for Custom Metaboxes and Fields for WordPress
Version: 1.0.0
Author: Florian Beck
Author URI: http://florianbeck.de
*/

define( 'CMB_TAGS_URL', plugin_dir_url( __FILE__ ) );
//define( 'CMB_TAGS_URL', dirname(get_stylesheet_uri()).'/inc/cmb/cmb-tags/' );
define( 'CMB_TAGS_VERSION', '1.0.0' );

// Render boxes
function cmb_tags_render( $field, $value, $object_id, $object_type, $field_type ) { 
	cmb_tags_enqueue(); ?>
	<?php echo $field_type->textarea( array(
		'desc' => '',
		'class' => 'show-if-no-js',
	) ); ?>
	<div class="ajaxtag hide-if-no-js">
		<input type="text" name="" class="new form-input-tip" size="16" autocomplete="off" value="" />
		<input type="button" class="button" value="<?php esc_attr_e('Add'); ?>" tabindex="3" />
	</div>
	<?php echo $field_type->_desc( true ); ?>
	<ul class="tagchecklist hide-if-no-js"></ul>
	<?php
}
add_filter( 'cmb2_render_tags', 'cmb_tags_render', 10, 5 );
add_filter( 'cmb2_render_tags_sortable', 'cmb_tags_render', 10, 5 );

// enque scripts

function cmb_tags_enqueue() {
	$asset_path = str_replace(
		array( WP_CONTENT_DIR, WP_PLUGIN_DIR ),
		array( WP_CONTENT_URL, WP_PLUGIN_URL ),
		dirname( __FILE__ )
	);
	wp_enqueue_script( 'cmb_tags_script', $asset_path . '/js/tags.js', array( 'jquery', 'jquery-ui-sortable' ),CMB_TAGS_VERSION );
	wp_enqueue_style( 'cmb_tags_stype', $asset_path . '/css/tags.css', array( 'pw-select2' ), CMB_TAGS_VERSION );
}

?>