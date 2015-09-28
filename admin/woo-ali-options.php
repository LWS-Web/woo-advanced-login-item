<?php

/*
 * Add page to the WP Settings menu
 */
function woo_ali_add_admin_menu() { 

	// Setting the global variable
	global $woo_ali_settings_page;

	$woo_ali_settings_page = add_options_page( 
		'Woo Advanced Login Item', // Page title
	 	'Woo Advanced Login Item', // Menu title
	 	'manage_options', // Capability
	 	'woo_ali_options', // Page slug
	 	'woo_ali_options_page' // Callback function
	);
}
add_action( 'admin_menu', 'woo_ali_add_admin_menu' );


/*
 * Building options page sections and setting fields
 */
function woo_ali_settings_init() { 

	register_setting( 'pluginPage', 'woo_ali_settings' );

	/* 
	 * Add "Menu Item Settings" section
	 */
	add_settings_section(
		'woo_ali_settings', 
		__( 'Menu Item Settings', 'woo-ali' ), 
		'woo_ali_section_callback', 
		'pluginPage'
	);

	/* 
	 * Add settings
	 */
	// Nav-Menu Select
	add_settings_field( 
		'wali_nav_select', 
		__( 'Select Navigation Menu', 'woo-ali' ), 
		'wali_nav_select_render', 
		'pluginPage', 
		'woo_ali_settings' 
	);

	// Position Select
	add_settings_field( 
		'wali_position', 
		__( 'Select Item Position', 'woo-ali' ), 
		'wali_position_render', 
		'pluginPage', 
		'woo_ali_settings' 
	);

	// Float Position
	add_settings_field( 
		'wali_float', 
		__( 'Select Float Position', 'woo-ali' ), 
		'wali_float_render', 
		'pluginPage', 
		'woo_ali_settings' 
	);

	// Content Alignment
	add_settings_field( 
		'wali_align', 
		__( 'Select Content Alignment', 'woo-ali' ), 
		'wali_align_render', 
		'pluginPage', 
		'woo_ali_settings' 
	);
	
}
add_action( 'admin_init', 'woo_ali_settings_init' );


/*
 * HTML templates for the single fields
 */
function wali_nav_select_render() { 
	$options = get_option( 'woo_ali_settings' ); ?>

	<select name="woo_ali_settings[wali_nav_select]">

		<option value="">- Please select a location -</option>

		<?php // Get all available menu locations
		$locations = get_nav_menu_locations();

			foreach ($locations as $location => $menu_id) { 

				if ($location != '0') { // Dont display the "0", is this Ok here?
					echo '<option value="'.$location.'" '.selected( $options['wali_nav_select'], $location ).'>'.$location.'</option>';
				}

			}//END foreach $locations ?>

	</select>

	<p><em>
		<?php _e('Note: The selected menu-location needs an active menu assigned. Otherwise the custom menu-item will not show.', 'woo-ali'); ?>
	</em></p>

<?php
} //END wali_nav_select_render()


function wali_position_render() { 
	$options = get_option( 'woo_ali_settings' ); ?>

	<select name="woo_ali_settings[wali_position]">
		<option value="after" <?php selected( $options['wali_position'], 'after' ); ?>>After</option>
		<option value="before" <?php selected( $options['wali_position'], 'before' ); ?>>Before</option>
	</select>

	<p><em>
		<?php _e('Select to display the menu-item before or after the default menu.', 'woo-ali'); ?>
	</em></p>

<?php
} //END wali_position_render()


function wali_float_render() { 
	$options = get_option( 'woo_ali_settings' ); ?>

	<select name="woo_ali_settings[wali_float]">
		<option value="default" <?php selected( $options['wali_float'], 'default' ); ?>>Default</option>
		<option value="right" <?php selected( $options['wali_float'], 'right' ); ?>>Right</option>
		<option value="left" <?php selected( $options['wali_float'], 'left' ); ?>>Left</option>
	</select>

	<p><em>
		<?php _e('Align the menu-item to the left or right. This may conflict with the "before" and "after" option.', 'woo-ali'); ?>
	</em></p>

<?php
}//END wali_float_render()


function wali_align_render() { 
	$options = get_option( 'woo_ali_settings' ); ?>

	<select name="woo_ali_settings[wali_align]">
		<option value="right" <?php selected( $options['wali_align'], 'right' ); ?>>Right</option>
		<option value="left" <?php selected( $options['wali_align'], 'left' ); ?>>Left</option>
	</select>

	<p><em>
		<?php _e('Align the dropdown content to the left or right.', 'woo-ali'); ?>
	</em></p>

<?php
}//END wali_align_render()


/*
 * Section callback
 */
function woo_ali_section_callback() { 
	echo __( 'Here you can change settings for the custom menu-item.', 'woo-ali' );
}//END woo_ali_section_callback()


/*
 * Building the options page layout
 */
function woo_ali_options_page() { ?>

	<div class="wrap">
		<h2><?php _e('Woo Advanced Login Item Settings', 'woo-ali'); ?></h2>
		<form action='options.php' method='post'>
		
			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>
		
		</form>
	</div>
	
	<?php
}//END woo_ali_options_page()