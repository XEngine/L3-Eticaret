@layout('layouts.default')

@section('content')
<div class="container-fluid">
    <div class="page-header">
    <h1>Hesap Bilgileri <small>Hesabınızla ilgili tüm ayarlarınızı buradan yapabilirsiniz!</small></h1>
    </div>
	<div class="row-fluid">
		<div class="span12">
			<blockquote>
				<p>Merhaba, <strong>{{Sentry::user()->metadata['first_name']}} {{Sentry::user()->metadata['last_name']}}!</strong></p>
				<small>Sepetinizde {{count(Cache::get('user_cart.'.Sentry::user()->id))}} adet ürün bulunmaktadır ve henüz A+ Kupon elde etmemişsiniz :(</small>
			</blockquote>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span2">
		    <ul class="nav nav-list">
				<li class="nav-header">Sipariş Bilgisi</li>
				<li class="active"><a href="#">Sipariş Takibi</a></li>
				<li><a href="{{URL::base()}}/cart">Sepetim({{count(Cache::get('user_cart.'.Sentry::user()->id))}})</a></li>
				<li><a href="#">İptal | Değişim | İade</a></li>
				<li class="nav-header">Beyaz Avantajlar</li>
				<li><a href="#">A+ Kuponlarım</a></li>
				<li class="nav-header">Bireysel Bilgiler</li>
				<li><a href="#">Bilgi Güncelle</a></li>
				<li><a href="#">Teslimat Adresi</a></li>
				<li><a href="#">Şifre Değiştir</a></li>
				<li><a href="#">Üyelik İptali</a></li>
			</ul>	
		</div>
		<div class="span10">
		<p class="lead">Mevcut Siparişiniz</p>
		<table class="table table-bordered row-fluid">
			<thead>
				<tr>
					<th style="width: 5%"><p class="text-center">Sipariş</p></th>
					<th style="width: 10%"><p class="text-left">Ürün Bilgileri</p></th>
					<th style="width: 5%"><p class="text-center">Adet</p></th>
					<th style="width: 10%"><p class="text-center">Fiyat</p></th>
					<th style="width: 7%"><p class="text-center">Kargo Bilgileri</p></th>
					<th style="width: 15%"><p class="text-center">Sipariş Durumu</p></th>
					<th style="width: 10%"><p class="text-center">Sipariş Tarihi</p></th>
				</tr>
			</thead>
			<tbody>
				@foreach($order as $item)
				<tr>
					<td><a href="/user/order/{{$hasher->encrypt($item->id)}}">{{$hasher->encrypt($item->id)}}</a></td>
					<?php 
					$images = getProductImages($item->getProduct);
					$price = getItemPrice($item->getProduct->price,$item->getProduct->getTax,$item->getProduct->getDiscount);
					?>
					<td>
					<div class="order-prod">
					<img alt="{{$item->getProduct->getDetail->name}}" src="<?php echo $images->main->tinym ?>">
					<p class="text-center"><strong><?php echo $item->getProduct->getDetail->name ?></strong></p>
					<span><?php echo $item->getProduct->getDetail->variant ?></span>
					</div>
					</td>
					<td>{{$item->productQuantity}}</td>
					<td><p class="text-error"><strong>{{$price->get}}</strong></p></td>
					<td>
						<p class="text-center text-info">{{$item->getShipment->name}}</p>
						<p class="text-center text-info">{{$item->getShipment->price}}TL</p>
					</td>
					<td>{{$item->shipmentStatus}}</td>
					<td>{{$item->created_at}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		</div>
	</div>
</div>

@endsection