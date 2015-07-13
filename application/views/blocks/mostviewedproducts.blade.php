<?php
//This is the top selling products so all we need is category id!
if($var === "all")
{
	$result = Category::with(array(
		"getProductsMostView",
		"getProductsMostView.getBrand",
		"getProductsMostView.getImages",
		"getProductsMostView.getDetail",
		"getProductsMostView.getTax",
		"getProductsMostView.getDiscount"
	))
	->get();
}else{
	$category_id = $var;
	$isTop = Category::find($var);
	if ($isTop->top == 1){
		//getting the products which belongs to this category!
		$children = Category::with("getChildren")->where("id","=",$var)->first();
		$childrenIDS = array();
		foreach($children->getChildren as $child){
			array_push($childrenIDS, $child->id);
			if($child->getChildren){
				foreach($child->getChildren as $subchild){
					array_push($childrenIDS, $subchild->id);
				}
			}
		}
		$result = Category::with(array(
			"getProductsMostView",
			"getProductsMostView.getBrand",
			"getProductsMostView.getImages",
			"getProductsMostView.getDetail",
			"getProductsMostView.getTax",
			"getProductsMostView.getDiscount"
		))
		->where_in('id',$childrenIDS)
		->get();
	}else{
		//getting the products which belongs to this category!
		$result = Category::with(array(
			"getProductsMostView",
			"getProductsMostView.getBrand",
			"getProductsMostView.getImages",
			"getProductsMostView.getDetail",
			"getProductsMostView.getTax",
			"getProductsMostView.getDiscount"
		))
		->where('id','=', $category_id)
		->first();
	}
}
?>
<div class="row-fluid">
	<div class="span12">
		<div class="block-header">
			<p>EN BEĞENİLEN ÜRÜNLER</p>
		</div>
	</div>
	<?php $counter = 0;?>
	<div class="row-fluid">
	@foreach($result as $res)
		@if($res->getProductsMostView)
			@foreach($res->getProductsMostView as $item)
			    @if($counter % 4 == 0)
				</div>
				<div class="row-fluid">
				@endif
				<?php $images = getProductImages($item) ?>
				@if($item->getImages)
					<?php $price = getItemPrice($item) ?>
				@endif
				<div class="span3">
				  <div class="thumbnail" style="padding: 0">
					<div style="padding:4px">
					@if($item->getImages)
						<a href="/product/<?php echo $item->alias ?>" class="thumbnailimg">{{HTML::image($images->main->tinym, $item->getDetail->name,array('style' => 'display:block;margin:0 auto;'))}}</a>
					@else
						<img style="display:block;margin:0 auto;" alt="300x200" style="width: 100%" src="http://placehold.it/200x150">
					@endif
					</div>
					<a href="/product/<?php echo $item->alias ?>">
						<div class="caption">
							<small><b>{{$item->getDetail->name}}</b></small>
							<p class="variant"><small>{{$item->getDetail->variant}}</small></p>
							@if($item->discount || $item->getDiscount)
							<p class="old-price">{{$price->get_taxed}}</p>
							<p class="new-price">{{$price->get_discount}}</p>
							@else
							<p class="new-price">{{$price->get_taxed}}</p>
							@endif
						</div>
					</a>
							@if($item->discount || $item->getDiscount)
						<div class="modal-footer" style="text-align: left">
						  <div class="row-fluid">
							@if($item->getDiscount && empty($item->discount))
							<div class="span12"><b class="text-info text-center" style="display:block;"><small>{{$item->getDiscount->description}}</small></b></div>
							@endif
							<div class="row-fluid">
								<div class="span6"><img src="/img/discount-badge-xs.png"><span class="discount-title"><b>{{($item->discount) ? $item->discount : $item->getDiscount->discount_value}}</b></span></div>
								<div class="span6" style="border-left:1px solid #ddd;padding-left:5px;"><b>Kazancınız</b><br/><small>{{$price->totaldiscount}}</small></div>
							</div>
						  </div>
						</div>
						@endif
						<a title='{{$item->getDetail->name}} {{$item->getDetail->variant}}' href="/product/<?php echo $item->alias ?>" class="free-shipment"></a>
				  </div>
				</div>
			<?php $counter++; ?>
			@endforeach
		@endif
	@endforeach
	</div>
</div>