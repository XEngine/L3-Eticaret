<?php

// 	Xysti Sitemap Config
// ------------------------------------------------
// Move this file to `application/config`
// ------------------------------------------------

return array(

	// 	Visible
	// ------------------------------------------------

	'home' => array(
		'title' => 'Home',
	),

	'about' => array(
		'title' => 'About',
		'redirect' => 'about/us',
		'/' => array(
			'us' => array(
				'title' => 'About Us'
			),
			'contact' => array(
				'title' => 'Contact Us'
			)
		)
	),

	// 	HIDDEN
	// -------------------------------------------------
	
	'login' => array(
		'title' => 'Sign In',
		'hidden' => TRUE,
		'post_validation' => array(
			'email' => 'required|email',
			'password' => 'required'
		),
		'post_login' => 'secure',
		'content' => 'secure.login'
	),

	'secure' => array(
		'title' => 'Dashboard',
		'hidden' => TRUE,
		'auth' => TRUE,
		'content' => 'secure.secure',
		'/' => array(
			'dashboard' => array(
				'title' => 'Dashboard',
				'redirect' => 'secure',
				'auth' => TRUE,
			),
			'phpinfo' => array(
				'title' => 'PHP Info',
				'template' => 'none',
				'auth' => TRUE,
			)
		)
	)
);