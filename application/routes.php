<?php

Route::get('category/(:all?)/product/(:all?)', 'product@index');
//Pop-Up Element
Route::get('product/(:all?)/friend-suggest', 'doajax@friend_suggest');

Route::get('product/(:all?)', 'product@index');
Route::post('/category', 'category@getCategory');
Route::get('category/(:any?)', array("as"   => "category",
                                     'uses' => 'category@index'
));

Route::get('user/register', 'user@register');
Route::get('user/activate', 'user@activate');
Route::get('user/login', 'user@login');
Route::post('user/login', 'user@login');
Route::get('cart', 'cart@index');
Route::post('cart', 'cart@ajaxBase');
Route::post('cart/remove', 'cart@ajaxremove');
Route::post('cart/addproduct', 'cart@JXcreate');
Route::get('checkout', 'checkout@index');
Route::get('checkout/address', 'checkout@address');
Route::get('checkout/payment', 'checkout@payment');
Route::post('checkout/payment/complete', 'checkout@paymentcomplete');
Route::post('checkout/setAddress', 'checkout@setAddress');
Route::post('bayi/payment/getbank', 'bayi@getbank');
/*Ajax*/
//User Town
Route::post('doajax/user/town', 'doajax@user_town');
//Agreement
Route::get('doajax/user/agreement', 'doajax@user_agreement');
Route::get('/gizlilik-politikasi', 'home@gizlilik');
Route::get('/kullanim-sartlari', 'home@kullanim');
//Pop-Up Element
Route::get('product/(:all?)/friend-suggest', 'doajax@friend_suggest');
/*Route For Administration*/
Route::get('admin', 'admin@index');
Route::get('admin/virtualpos', 'admin@virtualpos');
Route::get('admin/categories', 'admin@categories');
Route::get('admin/categories/(:num?)/(:all?)', 'admin@categories_edit');
Route::get('admin/product', 'admin@product');
Route::get('admin/product/new', 'admin@product_add');
Route::post('admin/product/new', 'admin@product_add');
Route::get('admin/product/edit/(:num?)/(:all?)', 'admin@product_edit');
Route::post('admin/product/product_add_get_attribute', 'admin@productAttribute');
Route::controller(Controller::detect());

/*Route::filter('pattern: ^(?!user/login)*', 'auth');*/
Event::listen('404', function () {
    return Response::error('404');
});

Event::listen('500', function () {
    return Response::error('500');
});

Route::filter('before', function () {
    Check_User_Cart();

    //Adding -> Assets
    Asset::container('styleSheet')
        ->add('mainStyle', 'css/style.css');
    Asset::container('bootstrap')
        ->add('BootstrapJS', 'js/bootstrap.js');
    Asset::container('megaMenu')
        ->add('menu', 'js/script.js');
    //Composer!
    $category = Category::with('getDescriptions')
        ->where('top', '=', '1')
        ->get();
    View::share('cat', $category);
    if (!Cache::has('settings')) {
        $settings = Setting::obtain();
        Cache::put('settings', $settings, 60);
    }
    $settings = Cache::get('settings');
    View::share('settings', $settings);
});

Route::filter('after', function ($response) {
    // Do stuff after every request to your application...
});

Route::filter('csrf', function () {
    if (Request::forged()) return Response::error('500');
});

Route::filter('auth', function () {
    if (!Sentry::check()) {
        Session::put('pre_login_url', URL::current());
        return Redirect::to('/user/login');
    }
});