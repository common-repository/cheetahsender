<?php

/**
 * This function sets the from email value
 */
if (!function_exists('wp_mail_smtp_mail_from')) :
function wp_mail_smtp_mail_from ($orig) {
	
	// This is copied from pluggable.php lines 348-354 as at revision 10150
	// http://trac.wordpress.org/browser/branches/2.7/wp-includes/pluggable.php#L348
	
	// Get the site domain and get rid of www.
	$sitename = strtolower( $_SERVER['SERVER_NAME'] );
	if ( substr( $sitename, 0, 4 ) == 'www.' ) {
		$sitename = substr( $sitename, 4 );
	}

	$default_from = 'wordpress@' . $sitename;
	// End of copied code
	
	// If the from email is not the default, return it unchanged
	if ( $orig != $default_from ) {
		return $orig;
	}
	
	if (defined('WPMS_ON') && WPMS_ON)
		return WPMS_MAIL_FROM;
	else if( get_option('mail_from') != "")
		return get_option('mail_from');
	
	// If in doubt, return the original value
	return $orig;
	
} // End of wp_mail_smtp_mail_from() function definition
endif;


/**
 * This function sets the from name value
 */
if (!function_exists('wp_mail_smtp_mail_from_name')) :
function wp_mail_smtp_mail_from_name ($orig) {
	
	// Only filter if the from name is the default
	if ($orig == 'WordPress') {
		if (defined('WPMS_ON') && WPMS_ON)
			return WPMS_MAIL_FROM_NAME;
		elseif ( get_option('mail_from_name') != "" && is_string(get_option('mail_from_name')) )
			return get_option('mail_from_name');
	}
	
	// If in doubt, return the original value
	return $orig;
	
} // End of wp_mail_smtp_mail_from_name() function definition
endif;

?>