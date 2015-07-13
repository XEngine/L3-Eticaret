<?php

class Cart_Controller extends Base_Controller {
	public $restful = true;

	private function getCache(){
		$cache = '';
		if(Sentry::check()){
			$cache = Cache::get('user_cart.'.Sentry::user()->id);
		}else{
			if(!Cookie::has('Anon_Cart_Extension') || !Cache::has('user_cart.'.Cookie::get('Anon_Cart_Extension'))){
				return false;
			}
			$cache = Cache::get('user_cart.'.Cookie::get('Anon_Cart_Extension'));
		}
		if($cache == null){
			return false;
		}
		return $cache;
	}
	private function getIdentifier(){
		$idt = '';
		if(!Sentry::check()){
			if(!Cookie::has('Anon_Cart_Extension')){
				Cookie::forever('Anon_Cart_Extension',get_unique_id());
			}
			$idt = Cookie::get('Anon_Cart_Extension');
		}else{
			$idt = Sentry::user()->id;
		}
		return $idt;
	}
	public function post_ajaxBase(){

		$getCartInformation = $this->getCache();
		
		$items = array();
		$prods = false;
		if($getCartInformation){
			foreach ($getCartInformation as $key => $value){
				array_push($items,$key);
			}
			$prods = Product::where_in('id',$items)->get();
		}

		return View::make('cart.ajaxbase')->with('product',$prods)->with('cache',$getCartInformation);
	}
	public function get_index(){
		Title::put('Alışveriş Sepeti');
		$getCartInformation = $this->getCache();
		$items = array();
		$prods = false;
		if($getCartInformation){
			foreach ($getCartInformation as $key => $value){
				array_push($items,$key);
			}
			$prods = Product::where_in('id',$items)->get();
		}
		return View::make('cart.index')->with('product',$prods)->with('cache',$getCartInformation);
	}

	public function post_JXcreate(){
		$data = Input::all();
		$product = Product::find((int)$data['id']);
		$price = getItemPrice($product);
		$images = getProductImages($product);
		$maxQty = $product->quantity;
		$quantity = ceil($data['stock']); // ceil for the abuse floats and decimals
		//Checking measures of our number if negative or is float or its more than our stock.
		if($quantity >= $maxQty){
			$quantity = $maxQty;
		}elseif($quantity < 0){
			$quantity = 1;
		}
		
		$Identifier = $this->getIdentifier();
		
		if(Cache::has('user_cart.'.$Identifier)){
			$currentcart = Cache::get('user_cart.'.$Identifier);
			if(array_key_exists($product->id,$currentcart)){
				$currentcart[$product->id]['_qty'] = $currentcart[$product->id]['_qty'] + $quantity; //999
				if($currentcart[$product->id]['_qty'] >= $maxQty){ 
					$currentcart[$product->id]['_qty'] = $maxQty;  //girilen sayı, toplam stoktaki sayıdan büyük veya eşitse her daim max stok sayısı gir.
				}

				Cache::forever('user_cart.'.$Identifier,$currentcart);
				$message = Lang::line('cart_lang.add_cart_fix',array('name' => $product->getDetail->name,'number' => $quantity))->get();
			}else{
				$currentcart[$product->id] = array('_sku' => $product->sku,'_qty' => $quantity);
				Cache::forever('user_cart.'.$Identifier,$currentcart);
				$message = Lang::line('cart_lang.add_cart',array('name' => $product->getDetail->name))->get();
			}
		}else{
			$arrayval = array(
				$product->id => array(
					'_sku' => $product->sku,
					'_qty' => $quantity
				));
			Cache::forever('user_cart.'.$Identifier,$arrayval);
			$message = Lang::line('cart_lang.add_cart',array('name' => $product->getDetail->name))->get();
		}
		$images = $images != null ? $images->main->tiny : null;
		return Response::json(array(
			'product_name' => $product->getDetail->name,
			'product_price' => $price,
			'variant' => $product->getDetail->variant,
			'img' => $images,
			'message'=> $message
		));
		
	}
	public function post_JXupdate(){
		return View::make('cart.jxupdate');
	}
	
	public function post_ajaxremove(){
		$data = Input::all();
		print_r($data);
		$currentcart = Cache::get('user_cart.'.Sentry::user()->id);
		unset($currentcart[$data['id']]);
		print_r($currentcart);
		Cache::forever('user_cart.'.Sentry::user()->id,$currentcart);
	}
}