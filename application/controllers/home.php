<?php

class Home_Controller extends Base_Controller {

	public function action_index()
	{
		return View::make('home.index');
	}
	public function action_gizlilik()
	{
		return View::make('user/gizlilik-politikasi');
	}
	public function action_kullanim()
	{
		return View::make('user/kullanim-sartlari');
	}
	public function action_ho(){
		    $vars = array(
        'email'    => 'test@test.com',
        'password' => '159753',
        'metadata' => array(
            'first_name' => 'John',
            'last_name'  => 'Doe',
        )
    );

    $user_id = Sentry::user()->create($vars,true);
	}
}