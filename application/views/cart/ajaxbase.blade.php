<!-- Listing in cart Items -->
@if($product)
<?php $total = 0; ?>
<div class="container-fluid">
    @foreach($product as $item)
	<?php 
		$images = getProductImages($item);
		$price = getItemPrice($item);
	?>
	<div class="row-fluid">
		<div class="span4">
			@if($images != null)
			<div class="cart_prod_img">
				<img class="img-rounded" src="<?php echo $images->main->tinym ?>" />
			</div>
			@endif
		</div>
		<div class="span8">
			<div class="cart_prod_desc">
				<dl>
					<dt>Ürün</dt>
					<dd><?php echo $item->getDetail->name ?></dd>
					<dt>Fiyat<?php if($item->getDiscount){ echo ' | İndirim '.$item->getDiscount->discount.'%';} ?></dt>
					<dd><strong><?php echo $price->get_taxed ?> x <?php echo $cache[$item->id]['_qty'] ?></strong></dd>
				</dl>
			</div>
		</div>
	</div>
	<?php $total = $total + $price->tax_raw*$cache[$item->id]['_qty']; ?>
    @endforeach
</div>
<div class="container-fluid cart-footer">
	<div class="row-fluid">
		<div class="span4"><a href="{{URL::base()}}/cart" class="btn btn-success btn-mini">Sepete git</a></div>
		<div class="span8">
			<div class="pull-right">
				<?php $tot = itemsTotal($cache); ?>
				<strong>Toplam : {{$tot->total}} </strong>
				@if($item->getTax)
				{{$item->getTax->title}} Dahil
				@endif
			</div>
		</div>
	</div>
</div>
@else
	<p>Alışveriş sepetinizde hiç ürün bulunmuyor!</p>
@endif