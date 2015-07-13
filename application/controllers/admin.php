<?php

class Admin_Controller extends Base_Controller {
	public $restful = true;
	public function get_index(){
		return View::make('administrator.index');
	}
	public function get_virtualpos(){
		return View::make('administrator.virtualpos');

	}
	public function get_categories(){
		$res = Category::with(array('getDescriptions','getTopCat','getTopCat.getDescriptions'))->get();
		return View::make('administrator.category')->with('result',$res);
	}
	public function get_categories_edit($id = 0,$alias = ''){
		$res = Category::with(array('getDescriptions','getTopCat','getTopCat.getDescriptions'))
		->where('id','=',$id)
		->first();

		$whole = Category::with('getDescriptions')->get();
		return View::make('administrator.category_edit')->with('result',$res)->with('all',$whole);
	}
	public function get_product(){
		$res = Product::paginate(50);
		return View::make('administrator.product')->with('result',$res);
	}
	public function get_product_edit($id = '',$alias = ''){
		$mode = Input::all();
		if($mode != null){
			switch($mode['mode']){
				case "imagedel" :
					return 'OK';
					break;
			}
		}
		if($id == null){
			$res = Product::paginate(50);
			return View::make('administrator.product')->with('result',$res);
		}
		/*PROD*/
		$productInformation = Product::where('id','=',$id)->first();
		/*EO PROD*/

		/*BRANDS */
		$brands = Brand::all();
		/* EO BRANDS*/

		/*TAXES*/
		$taxes = Tax::all();
		/*EO TAXES*/

		/*ALL CATEGORIES*/
		$categories = Category::where('top','=','1')
					  ->get();
		/*EO ALL CATEGORIES*/

		/*ALL TAGS*/
		$tags = DB::table("product_descriptions")->get('tag');
		$_tmpholder = array();
		$_strholder = '';
		foreach ($tags as $tag)
		{
			$_instance = explode(',',$tag->tag);
			foreach($_instance as $item)
			{
				array_push($_tmpholder,$item);
			}
		}
		$_tmpholder = array_unique($_tmpholder);
		$numItems = count($_tmpholder);
		$i = 0;
		foreach($_tmpholder as $item)
		{
			$_strholder .= '"'.$item.'"';
			if(++$i !== $numItems) {
				$_strholder .= ',';
			}
		}
		/*EO ALL TAGS*/	 

		/*ATTRS*/
			$result = Category::with(array(
				"getAttributeListing",
				"getAttributeListing.getTopGroup",
			))->where('id','=', $productInformation->getCategory[0]->id)->first();
			
			$topGroups = array();
			foreach($result->getAttributeListing as $item){
				 array_push($topGroups,$item->getTopGroup->id);
			}
			$topGroups = array_unique($topGroups);
			
			$belongedGroups = array();
			foreach($result->getAttributeListing as $item){
				 array_push($belongedGroups,$item->id);
			}
			$belongedGroups = array_unique($belongedGroups);

			/*Now we held only top groups($tmp) and Attributes groups($belongedGroups) only related to a category*/
			$attrs = AttributeGroup::with(array(
				'getParentGroup' => function($query) use($belongedGroups){
					$query->order_by('sort_order','ASC');
					$query->where_in('id',$belongedGroups);
				},
				'getParentGroup.getAttributes'
			))->where_in('id',$topGroups)->get();
		/*EO ATTRS*/

		return View::make('administrator.product_edit')
		->with('categories',$categories)
		->with('product',$productInformation)
		->with('brands',$brands)
		->with('tags',$_strholder)
		->with('taxes',$taxes)
		->with('attributes', $attrs);
	}
	public function get_product_add(){
		$categories = Category::where('top','=','1')
					  ->get();
		$products = Product::all();
		$brands = Brand::all();
		$tags = DB::table("product_descriptions")->get('tag');
		$_tmpholder = array();
		$_strholder = '';
		foreach ($tags as $tag)
		{
			$_instance = explode(',',$tag->tag);
			foreach($_instance as $item)
			{
				array_push($_tmpholder,$item);
			}
		}
		$_tmpholder = array_unique($_tmpholder);
		$numItems = count($_tmpholder);
		$i = 0;
		foreach($_tmpholder as $item)
		{
			$_strholder .= '"'.$item.'"';
			if(++$i !== $numItems) {
				$_strholder .= ',';
			}
		}
		$attributes = AttributeGroup::with('getChildrenGroup')->where('top','=',1)->get();
		$taxes = Tax::all();

		return View::make('administrator.product_add')
		->with('categories',$categories)
		->with('products',$products)
		->with('brands',$brands)
		->with('tags',$_strholder)
		->with('attributes',$attributes)
		->with('taxes',$taxes);
	}
	public function post_productAttribute(){
		$data = Input::all();
	//	$attrsTop = AttributeGroup::with(array('getTopGroup','getAttributes'))->where('id','=',$data['id'])->first();
			$result = Category::with(array(
				"getAttributeListing",
				"getAttributeListing.getTopGroup",
			))->where('id','=', $data['id'])->first();
			
			$topGroups = array();
			foreach($result->getAttributeListing as $item){
				 array_push($topGroups,$item->getTopGroup->id);
			}
			$topGroups = array_unique($topGroups);
			
			$belongedGroups = array();
			foreach($result->getAttributeListing as $item){
				 array_push($belongedGroups,$item->id);
			}
			$belongedGroups = array_unique($belongedGroups);

			/*Now we held only top groups($tmp) and Attributes groups($belongedGroups) only related to a category*/
			$attrs = AttributeGroup::with(array(
				'getParentGroup' => function($query) use($belongedGroups){
					$query->order_by('sort_order','ASC');
					$query->where_in('id',$belongedGroups);
				},
				'getParentGroup.getAttributes'
			))->where_in('id',$topGroups)->get();
		return Response::eloquent($attrs);
	}

	private function imageSizer($im,$cat){

		//Creating a temp image for background making it full white and 800x800
		$imageBG = imagecreatetruecolor(800, 800);
		$background = imagecolorallocate($imageBG, 255, 255, 255);
		imagefill($imageBG, 0, 0, $background);
		//Getting the image
        $info = pathinfo($im['name']);
		$xplode = explode(".",$im['name']);
		$name = $xplode[0].".jpg";
		
		//TODO::Add This to Config!
		$size = array("800"=>"800","500"=>"500","300"=>"300","200"=>"200","80"=>"80","40"=>"40");
		
        $extension = strtolower($info['extension']);
        if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {
            switch ($extension) {
                case 'jpg':
                    $topImage = imagecreatefromjpeg($im['tmp_name']);
                    break;
                case 'jpeg':
                    $topImage = imagecreatefromjpeg($im['tmp_name']);
                    break;
                case 'png':
                    $topImage = imagecreatefrompng($im['tmp_name']);
                    break;
                case 'gif':
                    $topImage = imagecreatefromgif($im['tmp_name']);
                    break;
                default:
                    $topImage = imagecreatefromjpeg($im['tmp_name']);
            }
            // load image and get image size
		}
		// Get image dimensions
		$baseWidth  = imagesx($imageBG);
		$baseHeight = imagesy($imageBG);
		$topWidth   = imagesx($topImage);
		$topHeight  = imagesy($topImage);
		
		$destX = ($baseWidth - $topWidth) / 2;
		$destY = ($baseHeight - $topHeight) / 2;
		if($topWidth <= 800 && $topHeight <= 800){
			imagecopy($imageBG, $topImage, $destX, $destY, 0, 0, $topWidth, $topHeight);
			imagejpeg($imageBG,'upload/'.$name);
		}
		else{
		    $success = Resizer::open(  $im )
					->resize( 800 , 800 , 'fit' )
					->save( 'upload/'.$name, 90 );
			if(!$success){return false;}
		}
		// Release memory
		imagedestroy($imageBG);
		imagedestroy($topImage);

		//Begin Resize and save the images!!!
		$fileDirectory = "public/img/products/".$cat->getDescriptions->alias.'/';
		$createBrandDir = File::mkdir($fileDirectory);
		$randomid = uniqid(mt_rand());
		foreach($size as $k => $v){
			$prodimgName = $randomid.".jpg";
			File::mkdir($fileDirectory.$k);
			$success = Resizer::open("upload/".$name)
					->resize( $k , $v , 'fit' )
					->save($fileDirectory.$k."/".$prodimgName, 100 );
			if(!$success){echo 'error';return false;}
		}
		return $randomid;
	}
	
	public function post_getBarcode(){
		$data = Input::all();
		$conn=mssql_connect("78.189.60.187","bilgiislem","eywallah!!123") or die("Couldn't connect to SQL Server");
		mssql_select_db("VEGADB",$conn) or die("Couldn't open database");
        $query = "SELECT IND AS ID, CAST(STOKKODU AS TEXT) AS STOKKODU FROM F0101TBLSTOKLAR WHERE MALINCINSI LIKE '%".$data['val']."%'";
		
		if(!(mssql_num_rows(mssql_query($query)) > 0)){
			return null;
		}
		$array = array();
        $StokCode = mssql_result(mssql_query($query,$conn),0,1);
		$array['stokcode'] = $StokCode;
		$_stokNo = mssql_result(mssql_query($query,$conn),0,0);
		
		$query = "SELECT CAST(BARCODE AS TEXT) AS Barcode FROM F0101TBLBIRIMLEREX WHERE STOKNO = '".$_stokNo."'";
		$Barcode = mssql_result(mssql_query($query,$conn),0,0);
		$array['barcode'] = $Barcode;
		
		return Response::json($array);
	}

	public function post_product_add(){
		$data = Input::all();
		$sessionArr = array("name" => "", "price" => "", "attrs" => array());
		$attributes = array();
		//check that product model is exist!
		$productCheck = Product::where('model','=',$data['p_model'])->first();
		
		if(empty($productCheck)){

						
			//first we fetch the brand and get the ID
			if(empty($data['p_brand_other'])){
				$brand = $data['p_brand']; //e.g $brand = 3;
			}else{
				//here we get brand ID by brand_other's input if the brand_other is filled
				$brandCheck = Brand::where("name",'=',$data['p_brand_other'])->first();
				if(empty($brandCheck)){
					$brand = DB::table('brands')->insert_get_id(array(
						'name' => $data['p_brand_other'],
						'alias' => Str::slug($data['p_brand_other'])
					));
				}else{
					$brand = $brandCheck->id;
				}
			}	
			$brandName = Brand::where("id",'=',$brand)->only('name');
			$brandalias = Brand::where("id",'=',$brand)->only('alias');
			
			$imageIDs = array();
			//pic
			$images = $data["userfile"];
			$mainImg = $data["active"];
			$CategoryfurImages = Category::with('getDescriptions')->where("id","=",$data['p_category'])->first();
			for ($i=0; $i < sizeof($images['name']); $i++) {
				$img = array(
					'name' => $images['name'][$i],
					'type' => $images["type"][$i],
					"tmp_name" => $images["tmp_name"][$i],
					"error" => $images["error"][$i],
					"size" => $images["size"][$i],
				);
				try{
					array_push($imageIDs,$this->imageSizer($img,$CategoryfurImages));
				}catch(Exception $e){
					Session::flash('status', 'Resim eklemede hata oluştu Ürünü tekrar ekleyiniz!\r\n'.$e->getMessage());
					return Redirect::to('admin/product/new');
				}

			}
			//now insert the product, first things first.
			$product = DB::table('products')->insert_get_id(array(
				'brand_id' => $brand,
				'model' => $data['p_model'],
				'price' => ceil($data['p_price'] / 1.18),
				'quantity' => $data['p_qty'],
				'tax_class_id' => $data['p_tax'],
				'barcode' => $data['p_barcode'],
				'alias' => trim(Str::slug($brandalias. ' ' . $data['p_model'])),
				'created_at' => DB::raw('NOW()'),
				'updated_at' => DB::raw('NOW()')
			));
			//inserting pictures 
			for($i = 0; $i < count($imageIDs);$i++){
				$imagesSql = DB::table('product_images')->insert(array(
					'product_id'  => $product,
					'main' => ($mainImg[$i] == 1 ? 1 : 0),
					'unique_id' => $imageIDs[$i]
				));
			}

			$productDescription = DB::table('product_descriptions')->insert_get_id(array(
				'product_id' => $product,
				'name' => $brandName.' '.$data['p_model'],
				'description' => $data['p_description'],
				'variant' => $data['p_variant'],
				'tag' => $data['p_tag'],
			));
			$sessionArr["name"] = $brandName.' '.$data['p_model'];
			$sessionArr["price"] = ceil($data['p_price'] / 1.18);
			//we insert the product now we set the category where the product belong
			$cagetory = DB::table('map_product_category')->insert_get_id(array(
				'product_id'  => $product,
				'category_id' => $data['p_category']
			));
			
			$attr_other = $data['attr_other'];
			$attrs = array();
			$check = true;
			foreach($data['attr'] as $k => $v){
				if(!empty($data['attr_other'][$k])){
					$CheckTheAttr = Attribute::where_attribute_group_id_and_value($k,$attr_other[$k])->first();
					if($CheckTheAttr == null){
						$attrotherID = DB::table('attributes')->insert_get_id(array(
							'attribute_group_id' => $k,
							'value' => $data['attr_other'][$k]
						));
						array_push($attributes,"Ekstra eklenen ve girilen ürün özelliği : ".$attr_other[$k]);
						$attrs[$k] = $attrotherID;
					}else{
						$attrs[$k] = $v;
					}
				}else{
					array_push($attributes,"Girilen ürün özelliği : ".$v);
					$attrs[$k] = $v;
				}
			}
			array_push($sessionArr["attrs"],$attributes);
			
			$attrsOrdered = array();
			foreach ($attrs as $k => $v){
				$attrsOrdered[] = array(
					'product_id' => $product,
					'attribute_id' => $v
				);
			}
			$attrs = DB::table('map_product_attribute')->insert($attrsOrdered);
		}else{
		    Session::flash('status', 'Bu ürün daha önce sisteme eklenmiş!');
			return Redirect::to('admin/product/new');
		}
		return Redirect::to('admin/product/new')->with("result",$sessionArr);
	}
}