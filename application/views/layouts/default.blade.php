<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{Title::get()}}</title>
	<?php
	//En Uygun Bilgisayar Fiyatları ve En İyi Bilgisayar Markaları için Hepsiburada&#39;nın sayısız model ve marka içeren bilgisayar sayfasını ziyaret edin.
	switch(Request::$route->controller){
		case 'product':
			echo '<meta name="description" content="'.$product->getProducts[0]->getDetail->name.' - '.$product->getProducts[0]->getDetail->variant.' - '.$product->getDescriptions->alias.' :: Beyazesyapazar.com"/>';
			echo '<meta name="keywords" content="'.$product->getProducts[0]->getDetail->tag.'"/>';
		break;
		case 'category':
			echo '<meta name="description" content="En Uygun '.$result->getDescriptions->name.' Fiyatları ve En İyi '.$result->getDescriptions->name.' Markaları için Beyazeşyapazar&#39;ın sayısız model ve marka içeren '.$result->getDescriptions->name.' sayfasını ziyaret edin. :: Beyazesyapazar.com"/>';
			echo '<meta name="keywords" content="'.$result->getDescriptions->name.'"/>';
		break;
		default:
			echo '<meta name="description" content="Sınırsız ürün yelpazesine sahip En İyi Markalar ve En iyi Fiyatlar için beyazesyapazar.com!">';
			echo '<meta name="keywords" content="beyazeşya, beyazeşya pazar, altus,regal,midea,online satış,b2b,b2c, beyaz eşya satış, online ticaret, uygun fiyat klima, ekonomik beyaz eşyalar">';
	}
	?>

	<meta name="viewport" content="width=device-width">
	<meta name="google-site-verification" content="vXAPINHWA1GsfUAYmKvHjCCDH9eJvmWCg5J2fr_m1dQ" />
	<link rel="shortcut icon" HREF="{{URL::base()}}/fav.ico">
	<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="{{URL::base()}}/css/bootstrap.css">
	<link rel="stylesheet" href="{{URL::base()}}/css/font-awesome.min.css">
	<link rel="stylesheet" href="{{URL::base()}}/css/toastr.min.css">
	<link rel="stylesheet" href="{{URL::base()}}/css/select2.css">
	<link rel="stylesheet" href="{{URL::base()}}/css/select2-bootstrap.css">

	<!-- CSS STYLES CORE -->
	{{Asset::container('styleSheet')->styles()}}

		<!-- JS MAINFRAME -->
	<script type="text/javascript" src="{{URL::base()}}/js/jquery.js"></script>
	<script src="{{URL::base()}}/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
	<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script> 
	<!-- LayerSlider styles -->
	<link rel="stylesheet" href="{{URL::base()}}/js/layerslider/css/layerslider.css" type="text/css">

</head>
<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/tr_TR/all.js#xfbml=1&appId=632526146796064";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!--[if lt IE 7]>
	<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> or <a href="https://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
<![endif]-->

	<div id="wrapper">
		{{----------------------------------------------------------------------------------------------}}
		{{------------------------------------------HEAD------------------------------------------------}}
		{{----------------------------------------------------------------------------------------------}}
			@include('partials.header')
		{{----------------------------------------------------------------------------------------------}}
		{{------------------------------------------BODY------------------------------------------------}}
		{{----------------------------------------------------------------------------------------------}}
			<div class="container">
				@yield('content')
			</div>
		{{----------------------------------------------------------------------------------------------}}
		{{------------------------------------------BODY------------------------------------------------}}
		{{----------------------------------------------------------------------------------------------}}
			@include('partials.footer')
	</div>

	<script type="text/javascript" src="{{URL::base()}}/js/jquery-ui.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/easing.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/simple-modal.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/i18n/messages.tr.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/parsley.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/bootstrap-formhelpers-phone.format.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/bootstrap-formhelpers-phone.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/jquery.menu-aim.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/jMenu.jquery.min.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/hoverIntent.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/cloudzoom.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/toastr.min.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/select2.min.js"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/liteaccordion.js"></script>
    <script type="text/javascript">
        $('#all-cat').liteAccordion({
        	containerWidth : 940,                   
			containerHeight : 305,
			theme : 'basic',
			headerWidth: 48
        });
	</script> 

	 
	<!-- jQuery with jQuery Easing, and jQuery Transit JS -->
	
	<script src="{{URL::base()}}/js/layerslider/jQuery/jquery-easing-1.3.js" type="text/javascript"></script>
	<script src="{{URL::base()}}/js/layerslider/jQuery/jquery-transit-modified.js" type="text/javascript"></script>
	 
	<!-- LayerSlider from Kreatura Media with Transitions -->
	<script src="{{URL::base()}}/js/layerslider/js/layerslider.transitions.js" type="text/javascript"></script>
	<script src="{{URL::base()}}/js/layerslider/js/layerslider.kreaturamedia.jquery.js" type="text/javascript"></script>
	<script src="{{URL::base()}}/js/URI.js" type="text/javascript"></script>
	<script type="text/javascript" src="{{URL::base()}}/js/core.js"></script>
</body>
</html>