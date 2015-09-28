<?php
/**
 * Plugin Name: Woo Advanced Login Item
 * Plugin URI: -
 * Description: Adds an advanced WooCommerce menu-item with register/sign-in options and my-account links. 
 * Version: 1.0.0
 * Author: Mo
 * Author URI: -
 * License: GPL2
 * Text Domain: woo-ali
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active so we can bail out early
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


/**
 * Load plugin textdomain
 **/
add_action( 'plugins_loaded', 'woo_ali_load_textdomain' );
function woo_ali_load_textdomain() {
  load_plugin_textdomain( 'woo-ali', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

/**
 * Enqueue the stylesheet, js (in wp_footer)
 **/
add_action('wp_enqueue_scripts', 'woo_ali_scripts');
function woo_ali_scripts() {
   	if ( ! is_admin() ) {
       	wp_enqueue_style( 'woo-ali', plugins_url('/css/style.css', __FILE__) );
    }  
}


/*
 * Include the admin page
 */
include_once( dirname( __FILE__ ) . '/admin/woo-ali-options.php' );


/**
 * Add the custom menu item function
 **/
add_filter( 'wp_nav_menu_items', 'woo_custom_login_item', 10, 2 );

/**
 * Custom menu item template
 **/
function woo_custom_login_item( $items, $args ) {

	ob_start(); // Start the output buffer

		// First we set some global attributes
		$my_account_url		= get_permalink( get_option('woocommerce_myaccount_page_id') ); // My-account link (overview)
		$edit_account_url 	= wc_customer_edit_account_url(); // Edit-account link (first and lastname, mail, password)
		$edit_address_url 	= wc_get_endpoint_url( 'edit-address', '', $my_account_url ); // Edit-address link (billing and shipping address)
		$logout_url 		= wc_get_endpoint_url( 'customer-logout', '', $my_account_url );

		// Get the custom admin-options
		$options = get_option( 'woo_ali_settings' );
		$nav_select = $options['wali_nav_select']; // Get the selected menu-slug
		$position = $options['wali_position'];
		$float = $options['wali_float'];
		$align = $options['wali_align'];

		echo '<style>';
	    		if ($float == 'right') {
	    			echo 'ul li.woo-cl-item { float: right; }';
	    		} elseif ($float == 'left') {
		    		echo 'ul li.woo-cl-item { float: left; }';
		    	}

		    	if ($align == 'right') {
		    		echo 'li.woo-cl-item .woo-cl-child { right:0; }';
		    	} elseif ($align == 'left') {
		    		echo 'li.woo-cl-item .woo-cl-child { left:0; }';
		    	}
	    	
	    echo '</style>';

		// If the current user is logged in...
		if (is_user_logged_in() && $args->theme_location == $nav_select) {

	    	//Get current user data (for wordpress versions > 3.4)
	    	$current_user 		= wp_get_current_user(); // Get user data array
			$user_login 		= $current_user->user_login; // Display user login
			$display_name 		= $current_user->display_name; // Display user display name (User's name, displayed according to the 'How to display name' User option)
			$user_firstname 	= $current_user->user_firstname; // Display user firstname
			$user_lastname 		= $current_user->user_lastname; // Display user lastname
			$user_mail 			= $current_user->user_email; // Display user email

			//See if we are on an WC account page, if yes, add the class
			if (is_account_page()) { $current_class = 'current-menu-item'; }

	    	?>

	        <li class="woo-cl-item logged-in menu-item <?php echo $current_class; ?>">
	        	<a class="account-link" href="<?php echo $my_account_url; ?>" title="<?php _e('My Account','woo-ali'); ?>">
					<?php echo $user_mail; ?>
				</a>

				<!-- Start of the sub-menu -->
	        	<div class="woo-cl-child sub-menu">

					<h2><?php printf( __('Hello  %1$s!', 'woo-ali'), $user_firstname ); ?></h2>

					<div class="customer-account-links">

						<a class="my-account-link button" href="<?php echo $my_account_url; ?>" title="<?php _e('My Account','woo-ali'); ?>">
							<?php _e('My Account','woo-ali'); ?>
						</a>

						<a class="edit-account-link" href="<?php echo $edit_account_url; ?>" title="<?php _e('Edit Account Details','woo-ali'); ?>">
							<?php _e('Edit Account Details','woo-ali'); ?>
						</a>

						<a class="edit-address-link" href="<?php echo $edit_address_url; ?>" title="<?php _e('Edit Address','woo-ali'); ?>">
							<?php _e('Edit Address','woo-ali'); ?>
						</a>

						<a class="logout-link" href="<?php echo $logout_url; ?>" title="<?php _e('Logout','woo-ali'); ?>">
							<?php _e('Logout','woo-ali'); ?>
						</a>

					</div>

	        	</div>
	        	<!-- End of the sub-menu -->
	        
	        </li>


	    <?php // If the current user is NOT logged in...
	    } elseif (!is_user_logged_in() && $args->theme_location == $nav_select) {
	    ?>

	        <li class="woo-cl-item logged-out menu-item">
	        	<a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e( 'Login', 'woo-ali' ); ?>">
					<?php _e( 'Login', 'woo-ali' ); ?>
				</a>

				<!-- Start of the sub-menu -->
				<div class="woo-cl-child">
					
						<div class="woo-cl-col register">
							<h2><?php _e( 'New Customer?', 'woo-ali' ); ?></h2>

							<p><?php $site_name = get_bloginfo( 'name' );
							printf( __('New to %1$s? Create an account to get started today.', 'woo-ali'), $site_name );
							#printf( __('Neu bei %1$s? Erstellen Sie ein Konto und legen Sie gleich los.', 'my-text-domain'), $site_name ); ?></p>

							<a class="button" href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e( 'Register', 'woo-ali' ); ?>">
								<?php _e( 'Register', 'woo-ali' ); ?>
							</a>
						</div>

						<div class="woo-cl-col login">

						<?php
						/**
						 * The following code is taken straight from the original WooCommerce login-form template. 
						 * We can not use "get_template", because we dont want to deal with the registration-form. 
						 * See: "plugins/woocommerce/templates/myaccount/form-login.php"
						 **/ ?>

						<!-- Start of the WooCommerce login-form -->

							<h2><?php _e( 'Login', 'woo-ali' ); ?></h2>

							<form method="post" class="login">

								<?php do_action( 'woocommerce_login_form_start' ); ?>

								<p class="form-row form-row-wide">
									<label for="username"><?php _e( 'Username or email address', 'woo-ali' ); ?> <span class="required">*</span></label>
									<input type="text" class="input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
								</p>
								<p class="form-row form-row-wide">
									<label for="password"><?php _e( 'Password', 'woo-ali' ); ?> <span class="required">*</span></label>
									<input class="input-text" type="password" name="password" id="password" />
								</p>

								<?php do_action( 'woocommerce_login_form' ); ?>

								<p class="form-row">
									<?php wp_nonce_field( 'woocommerce-login' ); ?>
									<input type="submit" class="button" name="login" value="<?php esc_attr_e( 'Login', 'woo-ali' ); ?>" />
									<label for="rememberme" class="inline">
										<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woo-ali' ); ?>
									</label>
								</p>
								<p class="lost_password">
									<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woo-ali' ); ?></a>
								</p>

								<?php do_action( 'woocommerce_login_form_end' ); ?>

							</form>

						<!-- End of the WooCommerce login-form -->

						</div>
					<div style="clear:both;"></div>
				</div>
				<!-- End of the sub-menu -->

	        </li>

	    <?php }//END is_user_logged_in

    $woo_custom_login_item = ob_get_clean(); // Store all output from above


    if ($position == 'after') {
    	$pos = $items . $woo_custom_login_item;
    } elseif ($position == 'before') {
    	$pos = $woo_custom_login_item . $items;
    }


    return $pos; // Add stored output to end of $items and return
}

}//END Check if WooCommerce is active