<?php

class Checkout_Controller extends Base_Controller {

	public $restful = true;
	
	public function __construct(){
		$this->filter('before', 'auth');
	}
	public function get_index()
	{
		$getCartInformation = Cache::get('user_cart.'.Sentry::user()->id);
		$items = array();
		if($getCartInformation){
			foreach ($getCartInformation as $key => $value){
				array_push($items,$key);
			}
		}else{
			return false;
		}
		$prods = Product::where_in('id',$items)->get();

		return View::make('checkout.index')->with('product',$prods)->with('cache',$getCartInformation);
	}
	public function get_address()
	{
		$AddressInfo = Address::with(array(
			'getCity','getTown'
		))
		->where('user_id','=',Sentry::user()->id)->get();
		$CartInfo = Cache::get("user_cart.".Sentry::user()->id);
		if(isset($CartInfo['Addresses']) && $CartInfo['Addresses']['delivery'] != null){
			$DefaultDelivery = Address::with(array('getCity','getTown'))->where('id','=',$CartInfo['Addresses']['delivery'])->first();
		}
		else{ $DefaultDelivery = null; }
		if(isset($CartInfo['Addresses']) && $CartInfo['Addresses']['billing'] != null){
			$DefaultBilling = Address::with(array('getCity','getTown'))->where('id','=',$CartInfo['Addresses']['billing'])->first();
		}
		else{ $DefaultBilling = null;	 }
		return View::make('checkout.address')
		->with('addresses',$AddressInfo)
		->with('defdelivery',$DefaultDelivery)
		->with('defbilling',$DefaultBilling);
	}
	public function post_paymentcomplete(){
		$data = Input::all();
		$SP = new SanalPos('halkbank');
		$request = $SP->Initialize('bonus');

		die(print_r($request));

		if($data['isThreeD']){

		}else{

		}
	    $adapter =& $vpos->factory($request->adapter);
	    $response = $adapter->complete($request);

	    die();
	    if ($response->succeed) {
	        var_dump($response);
	        echo "SUCCESS";
	    }
	    else {
	        throw new Exception($response->message);
	    }

	}
	public function get_payment(){
		$UserInfo = Sentry::user();
		$Cart = Cache::get("user_cart.".Sentry::user()->id);
		$DeliveryAddress = Address::with(array('getCity','getTown'))->where('id','=',$Cart['Addresses']['delivery'])->first();
		$BillingAddress =  Address::with(array('getCity','getTown'))->where('id','=',$Cart['Addresses']['billing'])->first();
		
		return View::make('checkout.payment')
		->with('user',$UserInfo)
		->with('Delivery',$DeliveryAddress)
		->with('Billing',$BillingAddress);
	}
	public function post_setAddress(){
		$data = Input::all();
		//get user cart cache
		
		$Cart = Cache::get("user_cart.".Sentry::user()->id);

		switch($data['type']){
			case 'delivery':
				$Cart['Addresses']['delivery'] = $data['addrID'];
			break;
			case 'billing':
				$Cart['Addresses']['billing'] = $data['addrID'];
			break;
		}
		Cache::forever("user_cart.".Sentry::user()->id,$Cart);
		$AddressInfo = Address::with(array(
			'getCity','getTown'
		))
		->where('id','=',$data['addrID'])->first();
		
		return Response::eloquent($AddressInfo);
	}
}