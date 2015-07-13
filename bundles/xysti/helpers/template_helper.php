<?php

//	Xysti Template Helper
//	Developed by Laurence Elsdon
//	elsdon.me
//	@iSpyCreativity
//	---------------------------------------------------
//	Changelog
//	2013-02-23 Beta Release
//	---------------------------------------------------


/**
 * Output nav
 * 
 * Generate the <li> elements for a Bootstrap 
 * navigation menu
 * @param array $args
 */
function nav($args = array()) {
	$args = array_merge(array(
		'echo' => TRUE,
		'start' => NULL,
		'depth' => 2
	), $args);

	$sitemap = Xysti::sitemap();
	$start = $sitemap;
	$parent = '';


	// If start is not the beginning..
	if($args['start'] && is_int($args['start'])):
		for($i = 1; $i <= $args['start']; $i++):
			$parent .= URI::segment($i) . '/';
			$start = $start[URI::segment($i)]['/'];
		endfor;
	endif;

	$output = nav_walker($start, $args, $parent);

	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * nav() walker function
 * @param array $sitemap
 * @param array $args
 * @param string $parent
 */
function nav_walker($sitemap, $args, $parent = '') {
	$output = '';
	foreach($sitemap as $slug => $page):
		if($slug == 'home') {
			$slug = '';
		}
		// Prep for page_meta
		$page['slug'] = $slug;

		if( ! Xysti::meta('hidden', $page)):
			// Format the URI
			$uri = $parent . $slug;
			$current_depth = Xysti::uri_count($uri);

			// If this page has children and we haven't reached the max depth
			if(isset($page['/']) && is_array($page['/']) && ($current_depth < $args['depth'])):
				$dropdown = TRUE;
			else:
				$dropdown = FALSE;
			endif;

			// Is this the active page
			if(URI::segment($current_depth) == $uri):
				$is_active = TRUE;
			else:
				$is_active = FALSE;
			endif;

			// Now the output
			$output .= '<li class="';
			if($dropdown) {
				$output .= 'dropdown';
			}
			if($is_active) {
				$output .= ' active';
			}
			//$output .= ' data-depth="' . $current_depth . '"';
			if(isset($page['href'])):
				$output .= '"><a href="' . Xysti::meta('href', $page) . '"';
			else:
				$output .= '"><a href="' . $uri . '"';
			endif;
			if($dropdown) {
				$output .= ' class="dropdown-toggle" data-toggle="dropdown"';
			}
			$output .= '>' . Xysti::meta('title', $page);
			if($dropdown) {
				$output .= ' <b class="caret"></b>';
			}
			$output .='</a>';
			if($dropdown):
				$output .= PHP_EOL . '<ul class="dropdown-menu">' . PHP_EOL;
				// Lets go deeper..
				$output .= nav_walker($page['/'], $args, $uri . '/');
				$output .= '</ul>' . PHP_EOL;
			endif;
			$output .='</li>' . PHP_EOL;
		endif;
	endforeach;
	return $output;
}


/**
 * Output breadcrumbs
 * 
 * Generate Bootstrap styled breadcrumbs 
 * @param array $args
 */
function breadcrumbs($args = array()) {
	$args = array_merge(array(
		'echo' => TRUE,
		'sep' => ' &rsaquo; '
	), $args);
	$output = '<li><a href="">Home</a></li>' . PHP_EOL;
	for($i = 1; $i < Xysti::uri_count(); $i++):
		$output .= '<li><span class="divider"> /</span><a href="' . URI::segment($i) . '">';
		$output .= Xysti::page('title', $i);
		$output .= '</a></li>' . PHP_EOL;
	endfor;
	$output .= '<li class="active"><span class="divider"> /</span>' . Xysti::page('title') . '</li>' . PHP_EOL;
	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * Output <title>
 * 
 * Generate <title>
 * @param array $args
 */
function head_title($args = array()) {
	$args = array_merge(array(
		// Set defaults here
		'echo' => TRUE,
		'home' => NULL,
		'sep' => ' &rsaquo; '
	), $args);
	$output = '';
	if($args['home'] && URI::is('home')):
		$output = $args['home'];
	else:
		for($i = Xysti::uri_count(); $i > 0; $i--):
			$output .= Xysti::page('title', $i) . $args['sep'];
		endfor;
	endif;
	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * Output heading
 * 
 * Generate <h1>, <h2> etc
 * @param array $args
 */
function page_title($args = array()) {
	$args = array_merge(array(
		// Set defaults here
		'echo' => TRUE,
		'a' => FALSE,
		'tag' => 'h1',
		'caption' => Xysti::page('caption'),
		'href' => URI::current(),
		'title' => Xysti::page('title')
	), $args);
	$output = '';
	$output .= '<' . $args['tag'] . '>';
	if($args['a']) {
		$output .= '<a href="' . $args['href'] .'">';
	}
	$output .= $args['title'];
	if($args['caption']) {
		$output .= ' <span class="caption">' . $args['caption'] .'</span>';
	}
	if($args['a']) {
		$output .= '</a>';
	}
	$output .= '</' . $args['tag'] . '>' . PHP_EOL;

	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * Output button
 * 
 * Generate a Bootstrap styled button
 * @param array $args
 */
function button($args = array()) {
	$args = array_merge(array(
		// Set defaults here
		'echo' => TRUE,
		'class' => NULL,
		'tag' => 'a',
		'value' => '',
		'href' => '#',
		'target' => FALSE,
		'icon' => 'download-alt',
		'after' => ''
	), $args);
	$input = in_array($args['tag'], array('submit', 'button'));
	$output = '';

	if($input):
		$output .= '<input class="btn"';
	else:
		$output .= '<' . $args['tag'];
	endif;

	$output .= ' class="btn ' . $args['class'] . '"';

	if($input):
		$output .= ' type="' . $args['tag'] . '" value="';
	else:
		$output .= 'href ="' . $args['href'] . '"';
		if($args['target'])  {
			$output .= ' target="' . $args['target'] . '"';
		}
		$output .= '>';
		if($args['icon'])  {
			$output .= '<i class="icon-' . $args['icon'] . '"></i> ';
		}
	endif;

	$output .= $args['value'];

	if($input):
		$output .= '>';
	else:
		$output .= '</a>';
	endif;

	if($args['after']) {
		$output .= $args['after'];
	}

	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * Output download buttons
 * 
 * Generate Bootstrap styled buttons for Xysti downloads
 * @param array $download_keys
 */
function downloads($data = array()) {
	$args = array_merge(array(
		// Set defaults here
		'echo' => TRUE,
		'target' => '',
		'wrap' => TRUE,
		'keys' => array()
	), $data);
	if(empty($args['keys'])) {
		$args['keys'] = $data;
	}
	$output = '';
	if($args['wrap']) {
		$output .= '<div class="downloads">';
	}
	$downloads = Config::get('downloads');
	foreach($args['keys'] as $key):
		if( ! empty($downloads[$key]['uri'])):
			$output .= '<div class="btn-group">' . PHP_EOL;
			$output .= button(array(
				'echo' => FALSE,
				'value' => $downloads[$key]['title'],
				'href' => Config::get('xysti.routes.downloads.read', 'view') . '/' . $key, 
				'target' => $args['target'],
				'icon' => 'file-alt',
				//'after' => '&nbsp;&nbsp&nbsp'
			)) . PHP_EOL;
			$output .= button(array(
				'echo' => FALSE,
				'href' => Config::get('xysti.routes.downloads.download', 'download') . '/' . $key
			)) . PHP_EOL;
			$output .= '</div>' . PHP_EOL;
		else:
			Log::write('warning', 'Could not find the download' . $key);
		endif;
	endforeach;
	if($args['wrap']) {
		$output .= '</div> <!-- .downloads -->';
	}
	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * Output thumbnails
 * 
 * Generate Bootstrap styled thumbnails
 * @param array $args
 * @param array $imgs
 */
function thumbnails($args = array(), $imgs = array()) {
	$args = array_merge(array(
		// Set defaults here
		'echo' => TRUE,
		'ul' => TRUE,
		'span' => 3,
		'lightbox' => TRUE,
		'tooltip' => TRUE,
		'timthumb' => FALSE,
		'imgs' => array()
	), $args);
	$output = '';
	
	if($args['ul']) {
		$output = '<ul class="thumbnails">' . PHP_EOL;
	}

	$imgs = array_merge($args['imgs'], $imgs);

	foreach($imgs as $img):

		if( ! is_array($img)) {
			$img = array('src' => $img);
		}

		$img = array_merge(array(
			// Set img defaults here
			'href' => $img['src'],
			'span' => $args['span'],
			'title' => FALSE,
			'full' => FALSE,
			'caption' => FALSE
		), $img);

		if($args['timthumb']):
			$tt = timthumb(array(
				'src' => $img['src'],
				'span' => $img['span'],
				'h' =>  (100 * $args['span'] - 30)
			));
			$img['full'] = timthumb(array(
				'src' => $img['src'],
				'wh' =>  TRUE
			));
			if($img['src'] == $img['href']) {
				$img['href'] = $tt;
			}
			$img['src'] = $tt;
		endif;


		$output .= '<li class="span' . $img['span'] . '">' . PHP_EOL;
		$output .= '<a href="' . $img['href'] . '" class="thumbnail" rel="';
		if($args['lightbox']) {
			$output .= ' lightbox';
		}
		if($args['tooltip']) {
			$output .= ' tooltip';
		}
		$output .= '"';
		if($img['title']) {
			$output .= ' title="' . $img['title'] . '"';
		}
		if($img['full']) {
			$output .= ' data-full="' . $img['full'] . '"';
		}
		$output .= '>' . PHP_EOL;
		$output .= '<img src="' . $img['src'] . '" />' . PHP_EOL;
		$output .= '</a>' . PHP_EOL;
		if($img['caption']) {
			$output .= '<div class="caption">' . $img['caption'] . '</div>';
		}
		$output .= '</li>' . PHP_EOL;

	endforeach;

	if($args['ul']) {
		$output .= '</ul> <!-- .thumbnails -->' . PHP_EOL;
	}

	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
}


/**
 * Output timthumb url
 * 
 * Generate a TimThumb link
 * @param array $args
 */
function timthumb($args = array()) {
	$args = array_merge(array(
		// Set defaults here
		'w' => FALSE,
		'h' => FALSE,
		'span' => FALSE,
		'src' => '',
		'wh' => FALSE
	), $args);
	$output = Config::get('xysti.timthumb') . '?src=' . $args['src'];
	if($args['wh']):
		$img['size'] = getimagesize('public/assets/img/' . $args['src']);
		if( ! is_array($args['wh'])) {
			$args['wh'] = array(1200, 800);
		}
		// If width greater than height
		if($img['size'][0] > $img['size'][1]):
			$args['w'] = $args['wh'][0];
		else:
			$args['h'] = $args['wh'][1];
		endif;
	endif;
	if($args['span']) {
		// Set it to the max size for a Bootstrap span
		$args['w'] = 100 * $args['span'] - 30;
	}
	foreach(array('a', 'h', 'w', 'q', 'a', 'zc', 'f', 's', 'cc', 'ct') as $value):
		if(isset($args[$value]) && $args[$value]) {
			$output .= '&' . $value . '=' . $args[$value];
		}
	endforeach;
	return $output;
}


/**
 * Output alerts
 * 
 * Generate Bootstrap style alerts from session data
 * @param array $args
 */
function alerts($args = array()) {
	$args = array_merge(array(
		// Set defaults here
		'echo' => TRUE,
		'dismiss' => TRUE
	), $args);
	$output = '';

	$alerts = array('warning', 'error', 'success', 'info');

	foreach($alerts as $alert):
		if(Session::get($alert)):
			$output .= '<div class="alert alert-' . $alert . '">';
			if($args['dismiss']) {
				$output .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
			}
			$output .= Session::get($alert) . '</div>';
			Session::flash($alert, FALSE);
			Session::forget($alert);
		endif;
	endforeach;
	
	if($args['echo']):
		echo $output;
	else:
		return $output;
	endif;
} // alerts()
