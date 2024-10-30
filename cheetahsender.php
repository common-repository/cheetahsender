<?php
/*
Plugin Name: CheetahSender For WordPress
Plugin URI: http://www.experian-cheetahmail.fr/
Description: Reconfigures the wp_mail() function to use CheetahSender SMTP instead of wp_mail() function.
Author: Experian CheetahMail
Version: 1.0.1
Author URI: http://www.experian-cheetahmail.fr/
License: GPLv2 or later
*/
// constants
define('DOMAIN_PLUGIN', 'cheetahsender');
define( "CURRENT_VERSION", '1.0.1' ); 
define('RELAY', 'relay.cheetahsender.com');
include('fn/fn.php');
// Array of options and their current values
global $wpms_options; 
$wpms_options = array (
	'mail_from' => get_option( 'mail_from', ''),
	'mail_from_name' => get_option( 'mail_from_name', ''),
	'mailer' => get_option( 'mailer', 'smtp'),
	'mail_set_return_path' => get_option( 'mail_set_return_path', '1'),
	'smtp_host' => get_option( 'smtp_host', 'localhost'),
	'smtp_port' => get_option( 'smtp_port', '25'),
	'smtp_ssl' => get_option( 'smtp_ssl', 'none'),
	'smtp_auth' => get_option( 'smtp_auth', false),
	'smtp_user' => get_option( 'smtp_user', ''),
	'smtp_pass' => get_option( 'smtp_pass', '')
);
/**
 * Activation function. This function creates the required options and defaults.
 */
if (!function_exists('cheetahsender_activate'))
{
	function cheetahsender_activate() 
	{	
		global $wpms_options;
		// check if all options are already into WP DB options table
		foreach ($wpms_options as $name => $val) {
			add_option($name,$val);		
		}	
	}
} // endif function exists
/**
 * Deactivation function. This function set the intial defaults options
 */
if (!function_exists('cheetahsender_deactivate'))
{
	function cheetahsender_deactivate() 
	{	
		global $wpms_options;
		// check if all options are already into WP DB options table
		update_option( 'mail_from', '');
		update_option( 'mail_from_name', '');
		update_option( 'mailer', 'mail');
		update_option( 'mail_set_return_path', '1');
		update_option( 'smtp_host', 'localhost');
		update_option( 'smtp_port', '25');
		update_option( 'smtp_ssl', 'none');
		update_option( 'smtp_auth', false);
		update_option( 'smtp_user', '');
		update_option( 'smtp_pass', '');
	}
} // endif function exists
if (!function_exists('phpmailer_init_smtp'))
{
	// This code is copied from wp-includes/pluggable.php as at version 2.2.2
	function phpmailer_init_smtp($phpmailer) 
	{		// If constants are defined, apply those options
		if (defined('WPMS_ON') && WPMS_ON) {
			
			$phpmailer->Mailer = WPMS_MAILER;
			
			if (WPMS_SET_RETURN_PATH)
				$phpmailer->Sender = $phpmailer->From;
			
			if (WPMS_MAILER == 'smtp') {
				$phpmailer->SMTPSecure = WPMS_SSL;
				$phpmailer->Host = WPMS_SMTP_HOST;
				$phpmailer->Port = WPMS_SMTP_PORT;
				if (WPMS_SMTP_AUTH) {
					$phpmailer->SMTPAuth = true;
					$phpmailer->Username = WPMS_SMTP_USER;
					$phpmailer->Password = WPMS_SMTP_PASS;
				}
			}			
			// If you're using contstants, set any custom options here			
		}
		else 
		{			
			// Check that mailer is not blank, and if mailer=smtp, host is not blank
			if ( ! get_option('mailer') || ( get_option('mailer') == 'smtp' && ! get_option('smtp_host') ) ) {
				return;
			}			
			// Set the mailer type as per config above, this overrides the already called isMail method
			$phpmailer->Mailer = get_option('mailer');			
			// Set the Sender (return-path) if required
			if (get_option('mail_set_return_path'))
				$phpmailer->Sender = $phpmailer->From;			
			// Set the SMTPSecure value, if set to none, leave this blank
			$phpmailer->SMTPSecure = get_option('smtp_ssl') == 'none' ? '' : get_option('smtp_ssl');			
			// If we're sending via SMTP, set the host
			if (get_option('mailer') == "smtp") {				
				// Set the SMTPSecure value, if set to none, leave this blank
				$phpmailer->SMTPSecure = get_option('smtp_ssl') == 'none' ? '' : get_option('smtp_ssl');				
				// Set the other options
				$phpmailer->Host = get_option('smtp_host');
				$phpmailer->Port = get_option('smtp_port');				
				// If we're using smtp auth, set the username & password
				if (get_option('smtp_auth') == "true") {
					$phpmailer->SMTPAuth = TRUE;
					$phpmailer->Username = get_option('smtp_user');
					$phpmailer->Password = get_option('smtp_pass');
				}
			}			
			// You can add your own options here, see the phpmailer documentation for more info:
			// http://phpmailer.sourceforge.net/docs/			
		}
		
	} // End of phpmailer_init_smtp() function definition
}// endif function exists

/**
 * This function outputs the plugin options page.
 */
if (!function_exists('cheetahsender_page')) 
{
	// Define the function
	function cheetahsender_page() 
	{	
		// Load the options
		global $wpms_options, $phpmailer;
		// on gère les valeurs null des input de saisie
		wp_nonce_field('email-options');
		include(dirname(__FILE__) . '/js/js_vars.php'); 
		?>
	<div class="cheetahsender_wrapper_outter">
		<div class="cheetahsender_wrapper">
			<div class="cheetahsender_logo">
				<img class="float_left" src="../wp-content/plugins/cheetahsender/img/logo.png" />
			</div>
			<div class="navigation-top float">                
				<ul class="main-menu">
					<li class="main-menu-item active"><a id="1" class="active" href="#"><span><?php _e('Welcome!', DOMAIN_PLUGIN); ?></span></a></li>
				</ul>
				<ul class="sub-menu"></ul>
			</div>
			<div class="cheetahsender_wrapper_inner">
				<h2 class="settings">
					<span><?php _e('CheetahSender for WordPress', DOMAIN_PLUGIN); ?></span>
				</h2>	
				<div class="form_wrapper">
					<div class="filter-menu-container">
						<ul class="filter-menu frequencies-filter heading">
							<li id="elt_1" class="cs_heading_elt active"><img src="../wp-content/plugins/cheetahsender/img/_last_sent_nl.png"> <?php _e('EMAIL FEATURES', DOMAIN_PLUGIN); ?></li>
							<li id="elt_2" class="cs_heading_elt"><img src="../wp-content/plugins/cheetahsender/img/smtp.png"><?php _e('SMTP SETTINGS', DOMAIN_PLUGIN); ?></li>
							<li id="elt_3" class="cs_heading_elt"><img src="../wp-content/plugins/cheetahsender/img/_big_bat.png"><?php _e('SEND A TEST', DOMAIN_PLUGIN); ?></li>
						</ul>
					</div>
					<div id="listContainer" class="list-container">
						<div class="toggelize" id="referred_1">
							<h3 class="emails"><?php _e('Mailer', DOMAIN_PLUGIN); ?></h3>
							<p>
								<label for="mailer_mail" class="half">
									<input id="mailer_mail" type="radio" name="mailer" value="mail" <?php checked('mail', get_option('mailer')); ?> />
									<?php _e('Use the wp_mail() native WordPress function to send emails.', DOMAIN_PLUGIN); ?>	
								</label>
								<label for="mailer_smtp" class="half">
									<input id="mailer_smtp" type="radio" name="mailer" value="smtp" <?php checked('smtp', get_option('mailer')); ?> />
									<?php _e('Send all WordPress emails via SMTP.', DOMAIN_PLUGIN); ?>
								</label>
							</p>                            
							<p>
								<label for="mail_set_return_path"  class="half tipsyer" original-title="<?php _e('Set the return-path to match the From Email',DOMAIN_PLUGIN); ?>" >
									<input   name="mail_set_return_path" type="checkbox" id="mail_set_return_path"  value="true" <?php checked('true', get_option('mail_set_return_path')); ?> />	
									<?php _e('Return Path', DOMAIN_PLUGIN); ?>
								</label>
							</p> 
							<div id="layout_smtp">							
								<div class="half_page">
									<p>
										<label for="mail_from"><?php _e('From Email', DOMAIN_PLUGIN); ?><span class="required"> *</span></label>
										<input class="tipsyer" original-title="<?php _e('You can specify the email address that emails should be sent from. If you leave this blank, the default email will be used.', DOMAIN_PLUGIN); ?>" type="text" id="mail_from" tabindex="1" name="mail_from" value="<?php print(get_option('mail_from')); ?>"  />
									</p>
								</div>
								<div class="half_page">
									<p>
										<label for="mail_from_name"><?php _e('From Name', DOMAIN_PLUGIN); ?> <span class="required"> *</span></label>
										<input class="tipsyer" original-title="<?php _e('You can specify the name that emails should be sent from. If you leave this blank, the emails will be sent from WordPress.', DOMAIN_PLUGIN); ?>" type="text" id="mail_from_name" tabindex="2" value="<?php print(get_option('mail_from_name')); ?>" name="mail_from_name"  />
									</p>
								</div>
							</div>
							<p>
								<span class="valid"><input type="button" id="cs_save_settings" value="<?php _e('Save Changes', DOMAIN_PLUGIN); ?>" class="cs_save_settings"></span>
							</p>
						</div>
						<div class="toggelize" style="display:none" id="referred_2">
							<h3 class="smtp">CheetahSender</h3>
							<p>
								<label for="smtp_host"><?php _e('SMTP Host', DOMAIN_PLUGIN); ?> <span class="required"> *</span></label>
								<input name="smtp_host" type="text" id="smtp_host" readonly="readonly" value="<?php print(RELAY); ?>" class="half" />
							</p>
							<p>
								<label for="smtp_port"><?php _e('SMTP Port', DOMAIN_PLUGIN); ?> <span class="required"> *</span></label>
								<input name="smtp_port" type="text" id="smtp_port" value="<?php print(get_option('smtp_port')); ?>" class="half" />
							</p>
							<p>
								<label for="smtp_user"><?php _e('Username', DOMAIN_PLUGIN); ?> <span class="required"> *</span></label>
								<input name="smtp_user" type="text" id="smtp_user" value="<?php print(get_option('smtp_user')); ?>" class="half" />
							</p>
							<p>
								<label for="smtp_pass"><?php _e('Password', DOMAIN_PLUGIN); ?> <span class="required"> *</span></label> 
								<input name="smtp_pass" type="text" id="smtp_pass" value="<?php print(get_option('smtp_pass')); ?>" class="half" />
							</p>
							<p>
								<label for="smtp_ssl_none" class="tipsyer" original-title="<?php _e('No encryption.', DOMAIN_PLUGIN); ?>">								
									<input id="smtp_ssl_none" type="radio" name="smtp_ssl" value="none" <?php checked('none', get_option('smtp_ssl')); ?> />
									 <?php _e('No encryption.', DOMAIN_PLUGIN); ?>
								</label>
								<label for="smtp_ssl_ssl" class="tipsyer" original-title="<?php _e('Use SSL encryption.', DOMAIN_PLUGIN); ?>">
									<input id="smtp_ssl_ssl" type="radio" name="smtp_ssl" value="ssl" <?php checked('ssl', get_option('smtp_ssl')); ?>   />	
									<?php _e('SSL', DOMAIN_PLUGIN); ?> <img src="../wp-content/plugins/cheetahsender/img/ssl.png" /> 
								</label>
								<label for="smtp_ssl_tls" class="tipsyer" original-title="<?php _e('Use TLS encryption. This is not the same as STARTTLS. For most servers SSL is the recommended option.', DOMAIN_PLUGIN); ?>">
								<input id="smtp_ssl_tls" type="radio" name="smtp_ssl" value="tls" <?php checked('tls', get_option('smtp_ssl')); ?>  /> 
								<?php _e('TLS', DOMAIN_PLUGIN); ?>	 <img src="../wp-content/plugins/cheetahsender/img/tls.png" />  
								</label>
							</p>
							<p>
								<span class="valid">
									<input type="button" name="submit" class="cs_save_settings" id="cs_save_settings" value="<?php _e('Save Changes',DOMAIN_PLUGIN); ?>" />
								</span>
							</p>
						</div>
						<div class="toggelize" style="display:none" id="referred_3">
							<h3 class="bat"><?php _e('Send a Test Email', DOMAIN_PLUGIN); ?></h3>
							<div class="search_page">
									<label for="to">@</label>
									<input name="to" original-title="<?php _e('Type your email address here', DOMAIN_PLUGIN); ?>" type="text" id="to" value="<?php echo get_option('admin_email',''); ?>" class="tipsyer" />
									<input type="button" name="wpms_action" id="cs_send_bat" class="" value="<?php _e('Send Test', DOMAIN_PLUGIN); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="cheetahsender_wrapper_footer">
				<img class="float_left" src="<?php echo plugins_url( dirname(plugin_basename(__FILE__))); ?>/img/footer-bg-left.png" />
				<img class="float_right" src="<?php echo plugins_url( dirname(plugin_basename(__FILE__))); ?>/img/footer-bg-right.png" />
			</div>
		</div>
	</div>	
<?php
	} // End of cheetahsender_options_page() function definition
} //endif founction exists


/**
 * This function adds the required page (only 1 at the moment).
 */
if (!function_exists('cheetahsender_menus'))
{
	function cheetahsender_menus() 
	{		
		if (function_exists('add_submenu_page')) {
			add_options_page(__('CheetahSender', 'cheetahsender'),__('CheetahSender', 'cheetahsender'),'manage_options',__FILE__,'cheetahsender_page');
		}		
	} // End of cheetahsender_menus() function definition
} //endif function exists

function cheetahsender_plugin_action_links( $links, $file ) 
{
	if ( $file != plugin_basename( __FILE__ ))
		return $links;

	$settings_link = '<a href="options-general.php?page=cheetahsender/cheetahsender.php">' . __( 'Settings', 'cheetahsender' ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

// Add an action on phpmailer_init
add_action('phpmailer_init','phpmailer_init_smtp');

if (!defined('WPMS_ON') || !WPMS_ON) 
{
	// Add the create pages options
	add_action('admin_menu','cheetahsender_menus');
	// Add an activation hook for this plugin
	register_activation_hook(__FILE__,'cheetahsender_activate');
	register_deactivation_hook(__FILE__,'cheetahsender_deactivate');
	// Adds "Settings" link to the plugin action page
	add_filter( 'plugin_action_links', 'cheetahsender_plugin_action_links',10,2);
}

	// Add filters to replace the mail from name and emailaddress
	add_filter('wp_mail_from','wp_mail_smtp_mail_from');
	add_filter('wp_mail_from_name','wp_mail_smtp_mail_from_name');

	if (isset($_GET['page']) && ($_GET['page'] == 'cheetahsender/cheetahsender.php'))
	{ 
	wp_enqueue_style("cheetahsender-styles", plugins_url( dirname(plugin_basename(__FILE__)) ."/css/cheetahsender.css", dirname( __FILE__ ) ) );
	wp_enqueue_style("cheetahsender-styles-global", plugins_url( dirname(plugin_basename(__FILE__)) ."/css/global_ecm.css", dirname( __FILE__ ) ) );	
	wp_enqueue_script("cheetahsender-tipsy", plugins_url( dirname(plugin_basename(__FILE__)) ."/js/jquery.tipsy.js", dirname( __FILE__ ) ) );	
	wp_enqueue_script("cheetahsender", plugins_url( dirname(plugin_basename(__FILE__)) ."/js/js.js", dirname( __FILE__ ) ) );			
	load_plugin_textdomain('cheetahsender', false, dirname(plugin_basename(__FILE__)) . '/languages/');
	}
	
	
	
	
	
	
	
	
	
/* OVERRIDE 	
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	$user = get_userdata( $user_id );

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
	$message .= '<br /><br />coke\r\n';

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);

}
 OVERRIDE */	



?>