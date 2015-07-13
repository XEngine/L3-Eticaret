<?php

class Category_Controller extends Base_Controller {

	public $restful = true;

	public function get_index($alias)
	{

		//We need Category ID for grabbing products
		$category_id = CategoryDescription::with('getCategory')
		->where('alias','=',$alias)
		->only('id');
        if($category_id == null){
            return \Laravel\Redirect::to('/');
        }
		//getting the products which belongs to this category!
        $data = Input::all();
		//Category attrs for filters. 
		$catAttr = Category::with(array(
			"getAttributeListing",
			"getAttributeListing.getAttributes",
		))->where('id','=', $category_id)
		->first();
		
		$filter = array();
        if(isset($data['Filter'])){
			foreach ($data['Filter'] as $cLine) {
				$kvp = explode('[-]', $cLine); // [0] = Enerji Sınıfı , [1] = A++
				$groupID = 0;
				$attrID = 0;
				foreach($catAttr->getAttributeListing as $item){
					if($kvp[0] === $item->name){
						$groupID = $item->id;
						foreach($item->getAttributes as $item2) {
							if($kvp[1] === $item2->value){
								$attrID = $item2->id;
								$filter[$kvp[0]] = Array("val" => $kvp[1],"id" => $attrID,'GID' => $groupID);
							}
						}
					}
				}
			}
		}
		$price_range = array();
		if(isset($data['Price_Range'])){
			$price_range = explode(' - ',$data['Price_Range']);
			if($price_range[0] == "2500TL ve üzeri"){
				$price_range[0] = 2500;
				$price_range[1] = 999999;
			}
		}
		$countforCache = Category::with('getProducts')->where('id','=',$category_id)->first()->getProducts()->count();

		if(Cache::has('category.products.'.$category_id)){
			$a = Cache::get('category.products.'.$category_id);
			$a = count($a->getProducts);
			if($a !== $countforCache){
				Cache::forget('category.products.'.$category_id);
			}
		}
		
		if(!Cache::has('category.products.'.$category_id)){
			$result = Category::with(array(
				"getProducts",
				"getProducts.getDetail",
				"getProducts.getBrand",
				"getProducts.getImages",
				"getProducts.getTax",
				"getProducts.getDiscount",
				"getProducts.getAttributes",
				"getChildren",
				"getChildren.getDescriptions",
				"getChildren.getProducts",
				"getSlideshow",
				"getSlideshow.getItems",
				"getAttributeListing",
				"getAttributeListing.getAttributes",
				"getAttributeListing.getAttributes.productSpecific",
			))
			->where('id','=', $category_id)
			->first();
			Cache::forever('category.products.'.$category_id,$result,3);
		}
		$result = Cache::get('category.products.'.$category_id);

		$products = array();
		$attributes = array();
		$brands = array();
		$price = array();
		$range = array();

		if(isset($filter)){
	        foreach($result->getProducts as $product){
				$mami = array();
				$prodPrice = getItemPrice($product)->tax_raw;
				foreach($product->getAttributes as $mam)
				{
					$mami[] = $mam->id;
				}				
				$invalid = FALSE;				
				foreach($filter as $filtresingle ){
					if ( array_search($filtresingle['id'], $mami) === FALSE)
					{
						$invalid = TRUE;
						break;
					}
				}
				if ($invalid == FALSE){
						//check for price range!
						if(!empty($price_range)){
							if($prodPrice > $price_range[0] && $prodPrice <= $price_range[1]){
								$brands[] = $product->getBrand->name;
								$price[] = getItemPrice($product)->tax_raw;
								foreach($product->getAttributes as $attribute){
									array_push($attributes,$attribute->id);
								}
								array_push($products,$product);
							}
						}else{
								$brands[] = $product->getBrand->name;
								$price[] = getItemPrice($product)->tax_raw;
								foreach($product->getAttributes as $attribute){
									array_push($attributes,$attribute->id);
								}
								array_push($products,$product);
						}
				}else{
					
				}
			}
		}
		if($price != null){
			$price = PriceRange::create($price);
		}
		if($brands != null){
			$brands = array_unique($brands);
		}
		
		$attributes = array_count_values($attributes);

		$attrgroup = array();
		foreach($result->getAttributeListing as $groups){
			foreach($groups->getAttributes as $attr){
				if(array_key_exists($attr->id,$attributes)){
					array_push($attrgroup,$groups->id);
				}
			}
		}
		Title::put($result->getDescriptions->name.' Kategorisi');
		return View::make('category.index')
		->with('result',$result)
        ->with('brands',$brands)
		->with('products',$products)
		->with('filters',$filter)
		->with('attribute_filter', $attributes)
		->with('ranges',$price)
		->with('selected_ranges',$price_range);
	}

	public function post_getCategory()
	{
		$categoryID = Input::get('menuid');
		
		$category = Category::with('getDescriptions')
		->where('parent_id','=',$categoryID)
		->or_where('id','=',$categoryID)
		->order_by('sort_order','ASC')
		->get();

		$getPopularSubs = Category::with('getDescriptions')
		->where('parent_id','=',$categoryID)
		->order_by('views','DESC')
		->take(9)
		->get();

		$view = View::make('category.parent_category');
		$view->categories = $category;
		$view->popular = $getPopularSubs;
		return Response::json(array('d'=>$view->render()));
	}


}