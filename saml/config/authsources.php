<?php

$blog_id = (string)get_current_blog_id();
$main_blog_id = '1';
//$wp_opt = get_option('saml_authentication_options');
$wp_opt = get_blog_option(1, 'saml_authentication_options'); // NOTE: should we use get_site_option('saml_authentication_options')

$blog_entityid = NULL;
$blog_details = get_blog_details($blog_id);
if ( !empty( $blog_details ) ) {
	//$blog_entityid = $blog_details->siteurl . "/wp-content/plugins/saml-20-single-sign-on/saml/www/module.php/saml/sp/metadata.php/" . $blog_id;
	$blog_entityid = network_site_url('/wp-content/plugins/saml-20-single-sign-on/saml/www/module.php/saml/sp/metadata.php/' . $main_blog_id, 'https');

}

$config = array(

	// This is a authentication source which handles admin authentication.
	'admin' => array(
		// The default is to use core:AdminPassword, but it can be replaced with
		// any authentication source.

		'core:AdminPassword',
	),


	// An authentication source which can authenticate against both SAML 2.0
	// and Shibboleth 1.3 IdPs.

	$blog_id => array(
		'saml:SP',
		'NameIDPolicy' => $wp_opt['nameidpolicy'],
		// The entity ID of this SP.
		// Can be NULL/unset, in which case an entity ID is generated based on the metadata URL.
		//'entityID' => NULL,
		'entityID' => $blog_entityid,
		'sign.authnrequest' => TRUE,
		'sign.logout' => TRUE,
		'redirect.sign' => TRUE,
		// The entity ID of the IdP this should SP should contact.
		// Can be NULL/unset, in which case the user will be shown a list of available IdPs.
		'idp' => $wp_opt['idp']
	)
);

// Cert and Key may not exist

if( file_exists( constant('SAMLAUTH_CONF') . '/certs/' . $main_blog_id . '/' . $main_blog_id . '.cer') )
{
	$config[$blog_id]['certificate'] = constant('SAMLAUTH_CONF') . '/certs/' . $main_blog_id . '/' . $main_blog_id . '.cer';
}

if( file_exists( constant('SAMLAUTH_CONF') . '/certs/' . $main_blog_id . '/' . $main_blog_id . '.key') )
{
	$config[$blog_id]['privatekey'] = constant('SAMLAUTH_CONF') . '/certs/' . $main_blog_id . '/' . $main_blog_id . '.key';
}
