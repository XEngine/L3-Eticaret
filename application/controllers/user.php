<?php

class User_Controller extends Base_Controller {
	public $restful = true;

	public function get_index(){
		return View::make('user.index');
	}
	public function get_register(){
		$getCity = City::all();
		$get1stTowns = Town::where('city_id','=',1)->get();
		
		return View::make('user.register')->with('city',$getCity)->with('towns',$get1stTowns);
	}
	public function post_register(){
		$rules = array(
			'regName' => 'required|match:/[a-z]+/',
			'regLastName' => 'required|match:/[a-z]+/',
			'regMail' => 'required|unique:users,email',
			'regMail2' => 'required',
			'regPassword' => 'required|max:18|min:3',
			'regPassword2' => 'same:regPassword',
			'regDate' => 'before:01/01/1998',
			'regCell' => 'required'
		);
	
		$vld = Validator::make(Input::all(),$rules);
		if($vld->fails()){
			$getCity = City::all();
			$get1stTowns = Town::where('city_id','=',1)->get();
			return View::make('user.register')->with('city',$getCity)->with('towns',$get1stTowns);
		}
		$data = Input::all();
		//Gender Validation
		if($data['regGender'] === 'Woman'){
			$data['regGender'] = 0;
		}elseif($data['regGender'] === 'Man'){
			$data['regGender'] = 1;
		}else{
			$data['regGender'] = -1;
		}
		//Date Validation
		$MySQLDate = date('Y-m-d', strtotime(str_replace('/', '-', $data['regDate'])));
		try
		{
			// create the user
			$user = Sentry::user()->register(array(
				'email'    => $data['regMail'],
				'password' => $data['regPassword'],
				'metadata' => array(
					'first_name' => $data['regName'],
					'last_name'  => $data['regLastName'],
					'birth_date' => $MySQLDate,
					'city' => $data['regCity'],
					'town' => $data['regTown'],
					'phone' => $data['regPhone'],
					'cell_phone' => $data['regCell'],
					'sex' => $data['regGender']
				)
			));
			if ($user)
			{
				// the user was created
				$_user = Sentry::user($user['id']);
				$link = URL::Base().'/user/activate/'.$user['hash'];
				$name = $_user->metadata['first_name'] .' '. $_user->metadata['last_name'];
				$to = $_user['email'];
				sendActivation($to,$name,$link);
			}
			else
			{
				return Response::error('500');
			}
		}catch (Sentry\SentryException $e)
		{
			return View::make('user.register')->with('error',$e->getMessage());
		}
		return View::make('user.register-complete');
	}
	public function get_activate($hash,$key){
		$activate_user = Sentry::activate_user($hash, $key);
		if($activate_user){
			try
			{
				// Force login
				Sentry::force_login($activate_user->email);
				return View::make('user.activation-complete')->with('error', false);
			}
			catch (Sentry\SentryException $e)
			{
				return View::make('user.activation-complete')->with('error', true);
			}
		}
		else{
			return View::make('user.activation-complete')->with('error', true);
		}
	}
	public function get_login(){
		return View::make('user.login');
	}
	
	public function post_login(){
		$data = Input::all();
		$rememberme = ((isset($data['inputRememberMe'])) ? true : false);

		try
			{
				if (Sentry::login($data['inputMail'],$data['inputPassword'], $rememberme))
				{
					if(Session::has('pre_login_url')) {
						$url = Session::get('pre_login_url');
						Session::forget('pre_login_url');
						return Redirect::to($url);
					} else {
						return Redirect::home();
					}
				}
				else
				{
				    Session::flash('login_errors','');
					return View::make('user.login');
				}
			}
			catch (Sentry\SentryException $e)
			{
				$errors = $e->getMessage();
				Session::flash('login_errors',$errors);
				return View::make('user.login');
			}
		
	}
	public function get_logout(){
		// log the user out
		Sentry::logout();
		return Redirect::home();
	}
	
	public function get_dashboard(){
		$order = Order::where_user_id(Sentry::user()->id)->get();
		$hasher = IoC::resolve('hashids');
		return View::make('user.dashboard')->with('order',$order)->with('hasher',$hasher);
	}
}