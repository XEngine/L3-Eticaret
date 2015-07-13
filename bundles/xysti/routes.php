<?php

//	Xysti Routes
//	Developed by Laurence Elsdon
//	elsdon.me
//	@iSpyCreativity
//	---------------------------------------------------
//	Changelog
//	2013-02-23 Beta Release
//  2013-03-03 Introduced named login routes
//	---------------------------------------------------


// 	Filters
// ------------------------------------------------


/**
 * Check whether redirect or auth required.
 * This is called before every route automatically.
 */
Route::filter('before', function()
{
	return Xysti::before();
});


/**
 * Validate input
 */
Route::filter('validate', function()
{
	return Xysti::validate();
});



// 	Permanent routes
// ------------------------------------------------


/**
 * Redirect the homepage
 */
Route::get('/', function()
{
	return Redirect::to('home');
});


/**
 * Error requests
 */
Route::get('error/(:num)', function($number)
{
	return Xysti::error($number);
});



// 	Sitemap.xml
// ------------------------------------------------

if(Config::get('xysti.routes.xml_sitemap')):

	function sitemap_xml_walk($sitemap, $parent = '') {
		$output = '';
		
		foreach($sitemap as $slug => $page):
			$uri = $parent . $slug;

			// If hidden
			if( ! Xysti::meta('disabled', $page) &&  ! Xysti::meta('auth', $page)):
				$output .= '<url>' . PHP_EOL;
				$output .= '<loc>' . URL::base() . '/' . $uri . '</loc>' . PHP_EOL;
				foreach(array('lastmod', 'changefreq', 'priority') as $attr):
					if(isset($page['sitemap'][$attr])) {
						$output .= '<' . $attr . '>' . $page['sitemap'][$attr] . '</' . $attr . '>' . PHP_EOL;
					}
				endforeach;
				$output .= '</url>' . PHP_EOL;
			endif;

			// If children
			if(isset($page['/']) && is_array($page['/'])):
				$output .= sitemap_xml_walk($page['/'], $uri . '/');
			endif;
		endforeach;
		
		return $output;
	}

	/**
	 * Sitemap.xml
	 */
	Route::get(Config::get('xysti.routes.xml_sitemap'), function()
	{
		$output = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
		$output .= sitemap_xml_walk(Xysti::sitemap());
		$output .= '</urlset>' . PHP_EOL;
		return Response::make($output, 200, array('Content-Type' => 'application/xml'));
	});

endif;


// 	Authentication routes
// ------------------------------------------------

if(Config::get('xysti.routes.auth')):


		
	/**
	 * Sign the user out and redirect
	 */
	if(Config::get('xysti.routes.auth.logout')):

		Route::get(Config::get('xysti.routes.auth.logout', 'logout'), array(
			'as' => 'logout',
			function()
		{
			$auth_driver = Config::get('xysti.auth', 'default');

			// Default auth
			if($auth_driver == 'default'):
				Auth::logout();
			// Sentry auth
			elseif($auth_driver == 'sentry'):
				Sentry::logout();
			endif;

			return Redirect::to_route('login')->with('info', 'You have been signed out');
		}));

	endif;


	/**
	 * Handle login
	 */
	if(Config::get('xysti.routes.auth.login')):

		Route::post(Config::get('xysti.routes.auth.login', 'login'), array(
			'before' => 'check|validate', 
			'as' => 'login',
			function()
		{
			$auth_driver = Config::get('xysti.auth', 'default');

			// Default auth
			if($auth_driver == 'default'):
				
				$login = Auth::attempt(array(
					'username' => Input::get('email'),
					'password' => Input::get('password')
				));

			// Sentry auth
			elseif($auth_driver == 'sentry'):

				try {
					$login = Sentry::login(
						Input::get('email'),
						Input::get('password'),
						Input::get('remember')
					);
				}
				catch(Sentry\SentryException $e) {
					Session::flash('error', $e->getMessage());
					$login = FALSE;
				}

			else:
				return Xysti::error(500, 'Unknown authentication driver.');
			endif;


			// Login was a success
			if($login):
				return Xysti::success_redirect();
			// Login failed..
			else:
				Session::flash('warning', 'User and password do not match');
			endif;

			return Xysti::make();
		}));

	endif;


	/**
	 * Handle registration attempts
	 */
	if(Config::get('xysti.routes.auth.register')):

		Route::post(Config::get('xysti.routes.auth.register', 'register'), array(
			'before' => 'check|validate', 
			'as' => 'register',
			function()
		{
			Xysti::helper('dbug');

			$auth_driver = Config::get('xysti.auth', 'default');

			// Default auth
			if($auth_driver == 'default'):
				
				Xysti::error(500, 'Default auth currently not configured for registration.');

			// Sentry auth
			elseif($auth_driver == 'sentry'):

				try {
					
					$user = Sentry::user()->create(array(
						'email' => Input::get('email'),
						'password' => Input::get('password'),
						'metadata' => array(
							'first_name' => Input::get('first_name'),
							'last_name'  => Input::get('last_name'),
						)
					));

					if($user):
						$registration = TRUE;
						try {
							Sentry::force_login($user);
						}
						catch(Sentry\SentryException $e) {
							Session::flash('error', $e->getMessage());
						}
					else:
						$registration = FALSE;
					endif;
				}
				catch(Sentry\SentryException $e) {
					$errors = $e->getMessage();
					Session::flash('error', $e->getMessage());
					$registration = FALSE;
				}

			else:
				return Xysti::error(500, 'Unknown authentication driver.');
			endif;


			// Registration was a success
			if($registration):

				// User activation email..
				if(0):
					$postmark = new Postmark();
					$postmark->to();
					$postmark->subject('Chim chim on the loose again');
					$postmark->txt_body('Hey Speed, Please keep Spritle and Chim chim in line. Love, Racer X.');
					$response = $postmark->send();
				endif;

				if(function_exists('registration_callback')) {
					return registration_callback();
				}

				return Xysti::success_redirect();

			// Registration failed..
			else:

				Session::flash('warning', 'Registration failed');

			endif;

			return Xysti::make();
		}));

	endif;


	/**
	 * Activate a new user and log them in
	 * @todo Finish email authentication mechanism
	 */
	if(FALSE && Config::get('xysti.routes.auth.activate')):

		Route::get('activate/(:any)/(:any)', function()
		{
			Xysti::helper('dbug');

			$auth_driver = Config::get('xysti.auth', 'default');

			// Default auth
			if($auth_driver == 'default'):
				
				Xysti::error(500, 'Default auth currently not configured for activation.');

			// Sentry auth
			elseif($auth_driver == 'sentry'):

				try {
					$activate_user = Sentry::activate_user(
						URI::segment(2),
						URI::segment(3),
						FALSE
					);
				}
				catch (Sentry\SentryException $e) {
					// issue activating the user
					// store/set and display caught exceptions such as a suspended user with limit attempts feature.
					$errors = $e->getMessage();
				}
			else:
				return Xysti::error(500, 'Unknown authentication driver.');
			endif;


			if($activate_user):
				//Sentry::force_login(URI::segment(2));
				return Redirect::to(Xysti::page('login', 'post_login'));
			else:
				return Xysti::make(500, 'User activation failed.');
			endif;

		});

	endif;

endif;


// 	Downloads routes
// ------------------------------------------------

if(Config::get('xysti.routes.downloads')):

	/**
	 * Download file
	 */
	if(Config::get('xysti.routes.downloads.download')):

		Route::get(Config::get('xysti.routes.downloads.download') . '/(:any)', function($request)
		{
			$downloads = Config::get('downloads');

			if(empty($downloads[$request])) {
				return Xysti::error(404, 'Download ' . $request . ' is not in config');
			}
			
			$download = $downloads[$request];

			// Run authentication etc on the download
			$before = Xysti::before($download);
			if( ! is_null($before)) {
				return $before;
			}

			$path = Config::get('xysti.resources.downloads') . $download['uri'];

			if( ! file_exists($path)) {
				return Xysti::error(404, 'Download ' . $request . ' could not be found at ' . $path);
			}

			return Response::download($path);
		});

	endif;

	/**
	 * View download file
	 */
	if(Config::get('xysti.routes.downloads.read')):

		Route::get(Config::get('xysti.routes.downloads.read') . '/(:any)', function($request)
		{
			$downloads = Config::get('downloads');

			if(empty($downloads[$request])) {
				return Xysti::error(404, 'Download ' . $request . ' is not in config');
			}
			
			$download = $downloads[$request];

			// Run authentication etc on the download
			$before = Xysti::before($download);
			if( ! is_null($before)) {
				return $before;
			}

			$path = Config::get('xysti.resources.downloads') . $download['uri'];

			if( ! file_exists($path)) {
				return Xysti::error(404, 'Download ' . $request . ' could not be found at ' . $path);
			}

			return Response::make(File::get($path), 200, array(
				'Content-Type' => File::mime(File::extension($path))
			));
		});

	endif;

endif;


// 	Catch all routes
// ------------------------------------------------


/**
 * Handle remaining GET requests
 */
Route::get('(.*)', function()
{
	return Xysti::make();
});


/**
 * Handle remaining POST requests
 */
Route::post('(.*)', function()
{
	return Xysti::error(500, 'No post route.');
});
