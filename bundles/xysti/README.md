# Xysti on [Laravel](http://laravel.com)

A feature rich content framework bundle for Laravel.

Xysti makes templating simpler. You provide a sitemap array containing page meta and Xysti's template helper generates your `<title>` tags, breadcrumbs, navigation menus and more.

Xysti's sitemap makes many tasks simpler, for example to require user authentication for a page just include `'auth' => TRUE` in the page meta. Need to hide a page from the navigation menu? `'hidden' => TRUE`

Xysti makes developing both static and dynamic websites a breeze a master template is automatically loaded with your page content nested.


## Features

- Flexible template helper functions
- Master template and content views
- Automated post data validation (requires Former)
- Easily generate login / logout pages
- Rich error views using your master template


## Installation

Installating Xysti only takes a few steps.
First, upload all the files to the bundles directory.
Second, copy the two config files `xysti.php` and `sitemap.php` to your `application/config` directory.
Finally, add xysti to your bundles array: `application/bundles.php`.

```php
return array(

	'xysti' => array( 'auto' => true )
	
);
```

## Configuration



## Documentation

### Page Meta
Page meta is the foundation of Xysti. The meta takes the form of a recursive array defined in `config/sitemap.php`. Each page is a key within the array with at least a `title` attribute.

####
- `title` `string` The page title you would like to use within the navigation menu, the `<title>` and `<h1>`
- `content` `string` The content view to load from the `views/content` directory. Default is `segment1.segment2.segment3`
- `redirect` `string` The URI will redirect to another page or another website. Default is `false`
- `hidden` `bool` Is the hidden in the navigation menu and in the `sitemap.xml`. Default is `false`. If `auth = true` the page will be hidden from non authenticated users.
- `disabled` `bool` If `true` the page will 404. Default is `false`
- `post_rules` `array` An array of POST validator rules. 
- `post_success` `string` The URI of the page to redirect to after successful POST validation.
- `/` `array` An array of child pages and their meta.

#### Examples

##### Static page
```php
return array(

	'about' => array(
		'title' = 'About Us'
	)

	
);
```

##### Child pages
```php
return array(

	'portfolio' => array(
		'title' => 'My portfolio',
		'/' => array(
			'web-design' => array(
				'title' => 'Website design'
			),
			'photography' => array(
				'title' => 'Photography'
			)
		)
	)
	
);
```

##### Dynamic page
```php
return array(

	'images' => array(
		'title' => 'Images',
		'/' => 'dynamic'
	)

	
);
```


##### `redirect` vs `href`
On the outset the `redirect` and `href` meta attributes may appear to do the same thing but it's a matter of visibility. `href` should be used to incorporate an link in a menu where a page has never existed; though it appears in the context with an apparent URI no URI exists at that place. `redirect` is used if you wish to mask the destination of a URI. For instance you may wish to redirect the URI `login` to `users/authentication/login`. Or it could be used as a short url for an external page. For instance `example.com/linkedin` could redirect to `http://www.linkedin.com/your-profile`.

##### POST validation page
```php
return array(
	
	'contact' => array(
		'title' => 'Contact us',
		'post_rules' => array(
			'email' => 'required|email',
			'message' => 'required'
		),
		'post_success' => 'thank-you'
	),
	
	'thank-you' => array(
		'title' => 'Thanks for getting in touch!'
	)
	
);
```

### Template Helper

#### nav( $args )
Generate the `<li>` elements for a Bootstrap navigation menu
- `$args['echo']` `bool` Echo or return the output. Default is `true`
- `$args['start']` `int` Start at level #. Default is `null`
- `$args['depth']` `int` Stop at level #. Default is `2`

#### breadcrumbs( $args )
Generate Bootstrap styled breadcrumbs
- `$args['echo']` `bool` Echo or return the output. Default is `true`

#### head_title( $args )
Generate `<title>`
- `$args['echo']` `bool` Echo or return the output. Default is `true`
- `$args['home']` `string` String to overwrite home title. Default is `null`
- `$args['sep']` `string` Separator. Default is ` &rsaquo; `

#### page_title( $args )
Generate `<h1>`, `<h2>` etc
- `$args['echo']` `bool` Echo or return the output. Default is `true`
- `$args['tag']` `string` Heading tag. Default is `h1`
- `$args['caption']` `string` Subtitle inside `<h>`. Default is `Xysti::page('caption')`
- `$args['a']` `bool` Include `<a>` tags. Default is `false`
- `$args['href']` `string` href of  `<a>`. Default is `URI::current()`
- `$args['title']` `string` The heading title. Default is `Xysti::page('title')`

#### button( $args )
Generate a Bootstrap styled button
- `$args['echo']` `bool` Echo or return the output. Default is `true`
- `$args['class']` `string` Additional classes. Default is `null`
- `$args['tag']` `string` `<a>`, `<input type="submit"` or `<input type="button"`. Default is `a`
- `$args['value']` `string` Button value. Default is ``
- `$args['href']` `string` Button link. Default is `#`
- `$args['target']` `string` Link target. Default is `false`
- `$args['icon']` `string` Bootstrap / Font Awesome icon. Default is `download-alt`
- `$args['after']` `string` Output after. Default is ``

#### downloads( $download_keys )
Generate Bootstrap styled buttons for Xysti downloads
- `$download_keys` `array` An array of Xysti download keys

#### thumbnails( $args, $imgs )
Generate Bootstrap styled thumbnails
- `$args['echo']` `bool` Echo or return the output. Default is `true`
- `$args['ul']` `bool` Wrap in `<ul>`. Default is `true`
- `$args['span']` `int` Thumbnail width. Default is `3`
- `$args['lightbox']` `bool` rel="lightbox". Default is `true`
- `$args['tooltip']` `bool` rel="tooltip". Default is `true`
- `$args['timthumb']` `bool` Run timthumb on each img. Default is `false`
- `$args['imgs']` `array` Array of images (merged with $imgs). Default is `array()`
- `$imgs[]['href']` `string` Thumbnail link. Default is `$img['src']`
- `$imgs[]['span']` `int` Thumbnail width. Default is `$args['span']`
- `$imgs[]['title']` `string` Title attribute. Default is `false`
- `$imgs[]['full']` `string` data-full attribute. Default is `false`

#### timbthumb( $args )
Generate a TimThumb link
- `$args['w'] `int` Width. Default is `false`
- `$args['h'] `int` Width. Default is `false`
- `$args['span'] `int` Override width with Bootstrap span. Default is `false`
- `$args['src'] `string` Img src relative to timthumb. Default is ``
- `$args['wh'] `int` Set width or height for lightbox. Default is `false`

#### alerts( $args )
Generate Bootstrap style alerts from session data
- `$args['echo']` `bool` Echo or return the output. Default is `true`
- `$args['dismiss']` `bool` Allow dismiss. Default is `true`
