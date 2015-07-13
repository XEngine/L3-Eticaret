<?php

//	Xysti
//	Developed by Laurence Elsdon
//	elsdon.me
//	@iSpyCreativity
//	---------------------------------------------------
//	Changelog
//	2013-02-23 Beta Release
//	---------------------------------------------------


/**
 * Xysti
 * 
 * A Laravel bundle adding a multitude of features.
 * http://elsdon.me
 * @version 1.0
 */
class Xysti {


	/**
	 * Sitemap array
	 * 
	 * The sitemap as collected and extended config
	 * @var array
	 */
	private static $sitemap;


	/**
	 * Cached page attributes
	 * @var array
	 */
	private static $page;


	/**
	 * Template / Content view strings
	 * @var string
	 */
	private static $views = array();


	/**
	 * Render a string instead of a content view
	 * @var string
	 */
	public static $content;


	/**
	 * Cached URI array
	 * @var array
	 */
	private static $uri_array;


	/**
	 * Helpers already included
	 * @var array
	 */
	private static $helpers = array();


	/**
	 * Data for use in views
	 * @var array
	 */
	public static $data = array();


	// 	Class assets
	// ------------------------------------------------


	/**
	 * Version number
	 * 
	 * Check current page number
	 * @return string
	 */
	public static function version()
	{
		return '1.0';
	}


	/**
	 * Include helper files
	 * 
	 * Plays a couple of tricks to help integrate
	 * CodeIgniter helper files
	 * @param string $helper The helper name excluding '_helper.php'
	 */
	public static function helper($helper)
	{
		// Only include if it's not been included before
		if(in_array($helper, Xysti::$helpers)) {
			return TRUE;
		}
		
		// Quick hack to use codeigniter helpers easier
		if ( ! defined('BASEPATH')) {
			define('BASEPATH', URL::base());
		}
		
		if(file_exists('bundles/xysti/helpers/' . $helper . '_helper.php')):
			include 'bundles/xysti/helpers/' . $helper . '_helper.php';
		elseif(file_exists('application/libraries/' . $helper . '_helper.php')):
			include 'application/libraries/' . $helper . '_helper.php';
		else:
			$error_message = 'Could not find helper ' . $helper . ' at ' . URI::current() . '.';
			Log::write('error', $error_message);
			// @todo better exit/error mechanism
			exit($error_message);
			return FALSE;
		endif;
		
		Xysti::$helpers[] = $helper;
		return TRUE;
		
	} // helper()

	
	/**
	 * Variable debug
	 * @param mixed $var
	 * @param bool $collapse
	 */
	public static function dbug($var, $collapse = FALSE)
	{
		if( ! in_array('dbug', Xysti::$helpers)) {
			Xysti::helper('dbug');
		}

		if($collapse):
			new dbug($var, '', FALSE);
		else:
			new dbug($var);
		endif;
	}



	// 	Request controllers
	// ------------------------------------------------


	/**
	 * Checks for auth / redirect
	 * 
	 * Checks wether the page needs authorisation or 
	 * is a redirect before 
	 * @param $meta Optional use meta other than current page
	 * @return string
	 */
	public static function before($meta = NULL)
	{
		
		// If no meta declared
		if(is_null($meta)):
			// Check for a stored page
			// There shouldn't be one, this is the first function called
			 if(empty(Xysti::$page)) {
				Xysti::$page = Xysti::sitemap_page_walk(Xysti::uri_array());
			}
			$meta = Xysti::$page;
		endif;


		// Is the page disabled?
		if(Xysti::meta('disabled', $meta)):
			if(Xysti::meta('disabled', $meta) === TRUE):
				return Xysti::error(404);
			else:
				return Xysti::page('disabled');
			endif;
		endif;
		

		// Is auth required
		if(Xysti::meta('auth', $meta)):
			if( ! Xysti::user_check()) {
				return Redirect::to('login', 403)
						->with('warning', 'You must be signed in to do that')
						->with('success_redirect', URI::current());
			}
		endif;

		// Is this a redirect
		if(Xysti::meta('redirect', $meta)) {
			return Redirect::to(Xysti::meta('redirect', $meta), 301);
		}
	}


	/**
	 * Perform input validation
	 * 
	 * Checks wether a page variable is set in the sitemap and returns it
	 * @return string
	 */
	public static function validate()
	{
		$rules = Xysti::page('post_rules');

		if(is_array($rules)):
			$validation = Validator::make(Input::all(), $rules);
		else:
			return Xysti::error(500, 'Expecting post rules array.');
		endif;

		// If validation has failed
		if($validation->fails()):
			Session::flash('warning', 'Could not submit. Validation errors were found.');
			// @todo remove Former dependance
			Former::withErrors($validation);
			// Make the page without any more routes
			return Xysti::make();
			// @todo some mechanism to redirect on failure if that's prefered.
			//return Redirect::to(URI::current())->with_errors($validation);
		endif;
	}

	/**
	 * Determine where to redirect
	 * 
	 * Used by login and register routes to determine where to redirect to on success
	 * @return redirect
	 */
	public static function success_redirect($default = 'home')
	{
		if(Session::get('success_redirect')):
			return Redirect::to(Session::get('success_redirect'));
		elseif(Xysti::page('post_success')):
			return Redirect::to(Xysti::page('post_success'));
		else:
			return Redirect::to($default);
		endif;
	}


	/**
	 * Render a view
	 * 
	 * Picks the template and the page content
	 * @param array $data
	 * @return string
	 */
	public static function make($data = array())
	{
		// Data to be bound to views
		Xysti::$data = array_merge(Xysti::$data, $data);
		
		// Which template?

		if(Xysti::page('template') == 'none'):
			Xysti::$views['template'] = FALSE;
			if(Xysti::page('content')):
				Xysti::$views['template'] = Xysti::page('content');
			else:
				Xysti::$views['template'] = 'content.' . URI::current();
			endif;
		elseif(Xysti::page('template')):
			Xysti::$views['template'] = Xysti::page('template');
		else:
			Xysti::$views['template'] = Config::get('xysti.template');
		endif;

		// What content?

		// Content must be in the sitemap
		if(Xysti::page('exists')):
			// Is content explicitly set?
			if(Xysti::page('content')):
				Xysti::$views['content'] = 'content.' . Xysti::page('content');
			// Is this an incorrecty configured dynamic page?
			elseif(Xysti::page('/') == 'dynamic'):
				return Xysti::error(500, 'Dynamic pages must have a content meta value specified.');
			// Else use the URI
			else:
				Xysti::$views['content'] = 'content.' . str_replace('/', '.', URI::current());
			endif;
		else:
			Log::write('info', 'No sitemap entry for ' . URI::current() . '.');
		endif;

		// Time to return a view!

		// Is the content set?
		if(isset(Xysti::$views['content']) OR isset(Xysti::$content)):
			// If there is a template then load it
			if(Xysti::$views['template']):
				return View::make(Xysti::$views['template'], Xysti::$data);
			// Else just load the content
			else:
				return View::make(Xysti::$views['content'], Xysti::$data);
			endif;
		// Else 404
		else:
			return Xysti::error(404);
		endif;
	}




	// 	View functions
	// ------------------------------------------------


	/**
	 * Error controller
	 * 
	 * Render a view for an error code
	 * @param int $file Optional HTTP status
	 */
	public static function error($error_code, $reason = NULL)
	{
		$errors = Config::get('xysti.errors');

		if(isset($errors[$error_code])):
			$error = $errors[$error_code];
			$error['code'] = $error_code;
		else:
			$error = $errors['generic'];
			$error['code'] = 'Generic';
		endif;

		Log::write('error', 'Error ' . $error['code'] . ' at ' . URI::current() . '. ' . $reason);

		if(View::exists('content.misc.error')):
			Xysti::$views['content'] = 'content.misc.error';
			Xysti::$data['error'] = $error;
		else:
			Xysti::helper('template');
			Xysti::$content = page_title(array(
				'echo' => FALSE,
				'title' => $error['title'],
				'caption' => $error['code']
			)) . PHP_EOL . $error['content'];
		endif;
		
		$view = View::make(Config::get('xysti.template'));

		if($error['code'] == 'Generic'):
			return $view;
		else: 
			return Response::make($view, $error['code']);
		endif;
	}


	/**
	 * Content render
	 * 
	 * Renders the content view or content string 
	 * @param array $args
	 * @return string
	 */
	public static function content($args = array()) {
		$args = array_merge(array(
			// Set defaults here
			'echo' => TRUE,
			'view' => ''
		), $args);

		if(empty(Xysti::$content)):
			if( ! empty($args['view'])):
				$output = render($args['view'], Xysti::$data);
			else:
				$output = render(Xysti::$views['content'], Xysti::$data);
			endif;
		else:
			$output = Xysti::$content;
		endif;
		
		if($args['echo']):
			echo $output;
		else:
			return $output;
		endif;
	}


	/**
	 * Partials render
	 * 
	 * Checks what to load then loads it or an error page
	 * @return bool
	 */
	public static function partial()
	{
		
	}



	// 	Data models 
	// ------------------------------------------------


	/**
	 * Return the sitemap
	 * 
	 * Checks whether the sitemap has been fetched, then returns it.
	 * @return array Xysti::$sitemap
	 */
	public static function sitemap()
	{
		if( ! is_array(Xysti::$sitemap)) {
			Xysti::$sitemap = Config::get('sitemap');
		}
		return Xysti::$sitemap;
	}


	/**
	 * Extend the sitemap
	 * 
	 * Checks whether the sitemap has been fetched, then returns it.
	 *  
	 * @param array $extension
	 * @return array Xysti::$sitemap
	 */
	public static function extend_sitemap($extension)
	{
		Xysti::$sitemap = array_merge_recursive(Xysti::sitemap(), $extension);
		// Reset the page cache.
		Xysti::$page = NULL;
		return Xysti::$sitemap;
	}
	
	
	/**
	 * Override the page
	 * 
	 * Override the $page variable.
	 * extend_sitemap() is the prefered method.
	 *  
	 * @param array $page
	 * @return array $page
	 */
	public static function page_override($page)
	{
		Xysti::$page = $page;
		return $page;
	}


	/**
	 * Page variable
	 * 
	 * Checks wether a page variable is set in the sitemap and returns it
	 * @param string $request The variable key to return
	 * @param mixed $uri Optional segment # or URI
	 * @return mixed
	 */
	public static function page($request, $uri = NULL)
	{

		// Use current page if no second argument
		if(is_null($uri)):
			// Check for a cached page
			if(is_null(Xysti::$page)) {
				Xysti::$page = Xysti::sitemap_page_walk(Xysti::uri_array());
			}
			// Take the page from the cache
			$page = Xysti::$page;
		// Segment number specified
		elseif(is_int($uri)):
			$page = Xysti::sitemap_page_walk(Xysti::uri_array(), $uri);
		// Segment string specified
		elseif(is_string($uri)):
			$page = Xysti::sitemap_page_walk(Xysti::uri_array($uri), Xysti::uri_count($uri));
		// Segment array specified
		elseif(is_string($uri)):
			$page = Xysti::sitemap_page_walk(Xysti::uri_array($uri), Xysti::uri_count($uri));
		else:
			//Log::write('error', 'Unexpected Xysti::page(' . $request . ',' . $uri . ') call at ' . URI::current() . '.');
			return Xysti::error(500, 'Unexpected Xysti::page(' . $request . ',' . $uri . ') call at ' . URI::current() . '.');
		endif;

		// Fetch the meta regardless of whether a sitemap entry has been found
		return Xysti::meta($request, $page);

		// Page was found
		// @todo Remove this if statement on confirmation of working
		if($page):
			return Xysti::meta($request, $page);
		else:
			//Log::write('error', 'Xysti::page(' . $request . ',' . $uri . ') could not be found at ' . URI::current() . '.');
			return FALSE;
		endif;
	}


	/**
	 * Walk the sitemap to find the requested page meta
	 * 
	 * @param array $segments
	 * @param int $segment_count Number of segments
	 * @return array Page meta
	 */
	private static function sitemap_page_walk($segments, $segment_count = NULL)
	{
		if(is_null($segment_count)) {
			$segment_count = Xysti::uri_count();
		}

		$walk = Xysti::sitemap();

		// Traverse the sitemap up to the $segment_count
		for($depth = 1; $depth <= $segment_count; $depth++):
			
			$this_segment = $segments[$depth - 1];

			// If we have reached the $segment_count
			if($depth == $segment_count):
				// Return item or 
				if(isset($walk[$this_segment])) {
					$walk[$this_segment]['slug'] = $this_segment;
					return $walk[$this_segment];
				}
				break;
			// If there are still children
			elseif(isset($walk[$this_segment]['/'])):
				// If ['/'] is an array keep traversing
				if(is_array($walk[$this_segment]['/'])):
					$walk = $walk[$this_segment]['/'];
				// If ['/'] == dynamic then all children equal this segment
				elseif($walk[$this_segment]['/'] == 'dynamic'):
					return $walk[$this_segment];
					break;
				// ['/'] is set but has clearly been done so incorrectly so end the loop
				else:
					break;
				endif;
			// If no children then break the loop
			else:
				break;
			endif;
		endfor;


		$page['slug'] = $this_segment;
		$page['not_found'] = TRUE;

		return $page;
	}

	/**
	 * Read meta from an array
	 * Bypassing walking the sitemap
	 * 
	 * @param array $request Page meta key
	 * @param array $page Page meta array
	 * @return mixed
	 */
	public static function meta($request, $meta)
	{
		// If all are sought
		if($request == 'all'):
			return $meta;
		// Does the page exist?
		elseif($request == 'exists'):
			// If there is no entry
			if(isset($meta['not_found']) && $meta['not_found']):
				return FALSE;
			// If it's just a menu item
			elseif(isset($meta['href']) && $meta['href']):
				return FALSE;
			else:
				return TRUE;
			endif;
		// If it's set return it.
		elseif(isset($meta[$request])):
			return $meta[$request];
		endif;
		
		// The meta has not been explicitly set so lets estimate it
		switch($request):
			case 'title':
				if(isset($meta['slug'])):
					return Str::title($meta['slug']);
				else:
					Log::write('error', 'page has no slug.. Something has been configured incorrectly.');
					return 'Error';
				endif;
			break;
			case 'hidden':
				// If the page requires authentication and the user is NOT logged in
				if(isset($meta['auth']) && $meta['auth'] && ! Xysti::user_check()):
					return TRUE;
				// Else if the page is disabled
				elseif(isset($meta['disabled']) && $meta['disabled']):
					return TRUE;
				endif;
			case 'href':
			break;
		endswitch;
		//Log::write('debug', 'Could not find Xysti::meta(' . $request . ') at ' . URI::current() . '.');
		return FALSE;
	}


	/**
	 * Fetch user meta
	 * 
	 * A wrapper for the various supported user drivers
	 * @param string $request The variable key to return
	 * @param int $user Optional user id
	 * @return mixed
	 */
	public static function user($request = NULL, $user = NULL)
	{
		if(is_null($request)):
			$request = 'id';
		elseif($request == 'full_name'):
			$full_name = TRUE;
			$request = 'metadata';
		endif;

		try {
			if(is_null($user)):
				$output = Sentry::user()->get($request);
			else:
				$output = Sentry::user($user)->get($request);
			endif;
		}
		catch(Sentry\SentryException $e) {
			Log::write('info', $e->getMessage());
			return FALSE;
		}

		if( ! empty($full_name)):
			return $output['first_name'] . ' ' . $output['last_name'];
		else:
			return $output;
		endif;
	}


	/**
	 * Check whether the user is logged in
	 * @return bool
	 */
	public static function user_check()
	{
		$auth_driver = Config::get('xysti.auth', 'default');
		
		// Default auth
		if($auth_driver == 'default'):
			return Auth::check();
		// Sentry auth
		elseif($auth_driver == 'sentry'):
			return Sentry::check();
		else:
			return Xysti::error(500, 'Unknown authentication driver.');
		endif;
	}



	// 	Framework extensions 
	// ------------------------------------------------


	/**
	 * Convert URI to Array
	 * @var string $uri
	 * @return string
	 */
	public static function uri_array($uri = NULL)
	{
		if( ! is_null(Xysti::$uri_array)):
			return Xysti::$uri_array;
		elseif(is_null($uri)):
			$uri = URI::current();
		endif;
		return explode('/', $uri);
	}

	/**
	 * Count URI segments
	 * @var string $uri
	 * @return int
	 */
	public static function uri_count($uri = NULL)
	{
		if(is_null($uri)) {
			$uri = URI::current();
		}
		return count(Xysti::uri_array($uri));
	}

}