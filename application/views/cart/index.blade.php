@layout('layouts.default')

@section('content')
<div class="container-fluid">
    <div class="page-header">
    <h1>Alışveriş Sepeti <small>Bu sepeti marketten dışarıya çıkartmak serbest!</small></h1>
    </div>
	<div class="row-fluid">
		<div class="span12">
			@if($product)
			<table class="table">
                <thead>
                <tr>
                  <th>Ürün</th>
                  <th>Adet</th>
                  <th>Fiyat (KDVsiz)</th>
                  <th>İndirim</th>
				  <th>Ödenecek Tutar</th>
				  <th>Kargo</th>
				  <th>İşlem</th>
                </tr>
				</thead>
				<tbody>
				@foreach($product as $item)
				<?php 
				$images = getProductImages($item);
				$price = getItemPrice($item);
				?>
				<tr data-prod="{{$item->id}}">
					<td>
					  <div class="media">
						<img class="media-object pull-left" src="<?php echo $images->main->tinym ?>">
						<div class="media-body" style="max-width:300px">
							<h4 class="media-heading"><?php echo $item->getDetail->name ?></h4>
							<?php echo $item->getDetail->variant ?>
						</div>
					</div>	
					</td>
					<td>
						<?php echo $cache[$item->id]['_qty'] ?>
					</td>
					<td>
						{{$price->get_price}}
					</td>
					<td>
						@if($item->getDiscount)
							<?php echo $item->getDiscount->description ?> - <?php echo $item->getDiscount->discount ?>%
							<br>
							<p class="text-center text-info">-{{$price->totaldiscount}}</p>
						@else
							<small>Bu ürün için bir kampanya bulunmamaktadır</small>
						@endif
					</td>
					<td>
						{{$price->get_taxed}}
					</td>
					<td></td>
					<td>
						<div class="pull-left">
							<a href="" onclick="return removeCartItem(event,this);">
								<i class="icon-remove"></i>
							</a>
						</div>
						<div class="pull-right">
							<input type="checkbox">
						</div>
					</td>
                </tr>
				@endforeach
				<tr>
					<td colspan="7">
						<strong>
						<ul class="inline big">
							<?php $_price = itemsTotal($cache); ?>
							<li><i class="icon-tag"></i> Toplam Tutar : <span class="muted">{{$_price->price}}</span></li>
							<li><i class="icon-tags"></i> Ödenecek Miktar : <span class="text-success">{{$_price->total}}</span></li>
						</ul>
						</strong>
						<div class="pull-right checkout-button">
							<a class="btn btn-large btn-success" href="{{URL::base()}}/checkout">Alışverişi Tamamla!</a>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			@else
				<p>Alışveriş sepetinizde hiç ürün bulunmuyor!</p>
			@endif
		</div>
	</div>
</div>

@endsection