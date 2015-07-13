<?php

class doajax_Controller extends Base_Controller {
	public $restful = true;
	
	public function post_user_town(){
		$data = Input::all();
		$towns = Town::where('city_id','=',$data['id'])->get();
		return Response::eloquent($towns);
	}
	public function get_user_agreement(){
		return View::make('user.agreement');
	}
	public function cart_addproduct(){
		$data = Input::all();

		$product = Product::find((int)$data['id']);
		if($product->getDiscount)
		{
			$price = getItemPrice($product->price,$product->getTax,$product->getDiscount);
		}else{
			$price = getItemPrice($product->price,$product->getTax);
		}
		$images = getProductImages($product);
		$maxQty = $product->quantity;
		$quantity = ceil($data['stock']); // ceil for the abuse floats and decimals
		
		if($quantity >= $maxQty){
			$quantity = $maxQty;
		}elseif($quantity < 0){
			$quantity = 1;
		}
		
	}
	public function get_friend_suggest(){

		return 'asd';
	}
}