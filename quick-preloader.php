<?php
/* 
Plugin Name: Quick Preloader
Plugin URI: http://sumonhasan.com/plugins/quick-preloader/
Description: This plugin will enable custom background color and custom preloader image url in your wordpress site.
Version: 1.0
Author: Sumon Hasan
Author URI: http://www.sumonhasan.com
*/

/*Some Set-up*/
define('QUICK_PRELOADER_WP', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );


function qploader_latest_jquery()
{
	wp_enqueue_script('jquery');
}
add_action('init', 'qploader_latest_jquery');

// Easy back to top options
function qploader_options_panel()  
{  
	add_options_page('Quick Preloader Options', 'Quick Preloader', 'manage_options', 'quick-preloader-options','qploader_options_framwrork');  
}  
add_action('admin_menu', 'qploader_options_panel');

// Easy back to top wp color picke
add_action( 'admin_enqueue_scripts', 'qploader_color_pickr_function' );
function qploader_color_pickr_function( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/color-pickr.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

// Default options values
$qploader_options = array(
	'bgcolor' => '#333',
);

if ( is_admin() ) : // Load only if we are viewing an admin page

function qploader_settings_register() {
	// Register settings and call sanitation functions
	register_setting( 'ebtt_p_options', 'qploader_options', 'qploader_validate_options' );
}

add_action( 'admin_init', 'qploader_settings_register' );

// Function to generate options page
function qploader_options_framwrork() {
	global $qploader_options;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false; // This checks whether the form has just been submitted. ?>

	<div class="wrap">

	<h2>Quick Preloader Options</h2>

	<?php if ( false !== $_REQUEST['updated'] ) : ?>
	<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
	<?php endif; ?>

	<form method="post" action="options.php">
	<?php $settings = get_option( 'qploader_options', $qploader_options ); ?>
	<?php settings_fields( 'ebtt_p_options' ); ?>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="bgcolor">Preloader background color</label></th>
			<td>
				<input id="bgcolor" type="text" name="qploader_options[bgcolor]" value="<?php echo stripslashes($settings['bgcolor']); ?>" class="wpd-color-field" /><p class="description">Select your preloader background color </p>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row"><label for="link_img">Preloader custom image url</label></th>
			<td>
				<input id="link_img" type="text" name="qploader_options[link_img]" value="<?php echo stripslashes($settings['link_img']); ?>" class="wpd-color-fields" /><p class="description">Insert your preloader custom image url here. you can make it fit your site better by generating your own image <a target="_blank" href=" http://ajaxload.info/">here: </a></p>
			</td>
		</tr>
		
	</table>
	<p class="submit"><input type="submit" class="button-primary" value="Save Options" /></p>

	</form>
	</div>

	<?php
}

function qploader_validate_options( $input ) {
	global $qploader_options;

	$settings = get_option( 'qploader_options', $qploader_options );
	
	// We strip all tags from the text field, to avoid vulnerablilties like XSS

	$input['bgcolor'] = wp_filter_post_kses( $input['bgcolor'] );
	$input['link_img'] = wp_filter_post_kses( $input['link_img'] );

	

	return $input;
}

endif;  // EndIf is_admin()


function active_qploader() {?>

<?php global $qploader_options; $qploader_settings = get_option( 'qploader_options', $qploader_options ); ?>
	<script type="text/javascript"> 
		jQuery(document).ready(function(jQuery) {  
		jQuery(document).ready(function($){
			jQuery('.wpd-color-field').wpColorPicker();
		});
		});
		
	</script>
	<script type="text/javascript"> 
		jQuery(window).load(function(){
			jQuery('#preloader').fadeOut('slow',function(){jQuery(this).remove();});
		});
	</script>
	<?php

	if (empty( $qploader_settings['link_img'] ) ) {
		$difault_img = plugins_url('img/loading.gif', __FILE__);
	}

	 ?>
	<style type="text/css"> 
		#preloader { 
		position: fixed;
		left: 0;
		top: 0;
		z-index: 999;
		width: 100%;
		height: 100%;
		overflow:visible;
		background-color:<?php echo $qploader_settings['bgcolor']; ?>;
		background-image:url(<?php echo $qploader_settings['link_img']; ?><?php echo $difault_img ?>);
		background-repeat:no-repeat;
		background-attachment:center center;
		background-position:center;
	 }
	</style>
	
<?php

}

// Add div class in footer
add_action('wp_footer', 'active_qploader');
function add_quickpreloader_div_class() {
    echo '<div id="preloader"></div>';
}
add_action( 'wp_footer', 'add_quickpreloader_div_class', 100 );

	