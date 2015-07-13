@layout('layouts.administrator')

@section('content')
<div class="container-fluid">
	<!-- BEGIN PAGE HEADER-->
	<div class="row-fluid">
		<div class="span12">
			<h3 class="page-title">
				Kategori Yönetimi<small>	Ürün kategorileri</small>
			</h3>
		</div>
	</div>
	<?php echo $result->links(); ?>
	<a href="/admin/product/new">Yeni</a>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="row-fluid">
		<div class="span12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>#</th>
						<th>SKU</th>
						<th>Ürün Adı</th>
						<th>Marka / Model</th>
						<th>Kampanya</th>
						<th>Fiyat</th>
						<th>Stok Adet</th>
						<th>Araçlar</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($result->results as $item){ ?>
					<tr>
				        <td>{{$item->id}}</td>
				        <td><div id="categoryname-<?php echo $item->id; ?>">{{$item->sku}}</div></td>
				        <td>{{$item->getBrand->name}} {{$item->model}}</td>
				        <td>{{$item->getBrand->name}} / {{$item->model}}</td>
				        <td>@if($item->getDiscount)
				        		{{$item->getDiscount->description}} // %{{$item->getDiscount->discount}}
				        	@else
				        		Ürüne ait kampanya bulunmamaktadır
				        	@endif
				        </td>
				        <td>{{$item->price}}</td>
						<td>{{$item->quantity}}</td>
						<td>
				        	<a href="/admin/product/edit/<?php echo $item->id.'/'.$item->alias;?>"><i class="icon-pencil"></i></a>
				        	<a href=""><i class="icon-remove-circle"></i></a>
				        </td>
			        </tr>
			        <?php } ?>
			    </tbody>
			</table>
		</div>
	</div>
</div>

@endsection