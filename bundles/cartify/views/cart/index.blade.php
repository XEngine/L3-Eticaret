@layout('cartify::template')

@section('content')
<form method="post" action="{{ URL::to('cartify/cart') }}" class="form-horizontal">
	<h3>Cart Contents</h3>
	<table class="table table-hover table-striped table-bordered">
		<thead>
			<tr>
				<th width="6%"></th>
				<th width="40%">Name</th>
				<th width="8%">Qty.</th>
				<th width="12%">Price</th>
				<th width="12%">Sub-Total</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($cart_contents as $item)
			<!-- Get the product options, you should get product related options on your controller! -->
			<?php $product_options = Cartify\Models\Products::get_options($item['id']); ?>

			<tr>
				<td class="align-center">
					<span class="span1 thumbnail"><img src="{{ URL::to_asset('bundles/cartify/products/' . $item['image']) }}" /></span>
				</td>
				<td>
					<strong><a href="{{ URL::to('cartify/view/' . Str::slug($item['name'])) }}">{{ $item['name'] }}</a></strong>

					<span class="pull-right">
						<a href="{{ URL::to('cartify/cart/remove/' . $item['rowid']) }}" rel="tooltip" title="Remove the product" class="btn btn-mini btn-danger"><i class="icon icon-white icon-remove"></i></a>
					</span>

					<!-- Check if this cart item has options. -->
					@if (Cartify::cart()->has_options($item['rowid']))
					<small>
						<ul class="unstyled">
						@foreach (Cartify::cart()->item_options($item['rowid']) as $option_name => $option_value)
							<li>- <small>{{ $option_name }}: {{ array_get($product_options, $option_name . '.' . $option_value) }}</small></li>
						@endforeach
						</ul>
					</small>
					@endif
				</td>
				<td><input type="text" class="span1" value="{{ $item['qty'] }}" name="items[{{ $item['rowid'] }}]" /></td>
				<td>{{ format_number($item['price']) }}</td>
				<td>{{ format_number($item['subtotal']) }}</td>
			</tr>
			@empty
			<tr>
				<td colspan="6">Your shopping cart is empty.</td>
			</tr>
			@endforelse
		</tbody>
	</table>

	@if (Cartify::cart()->total())
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td width="63%" colspan="3"></td>
				<td width="12%">Items</td>
				<td width="12%">{{ Cartify::cart()->total_items() }}</td>
			</tr>
			<tr>
				<td width="63%" colspan="3"></td>
				<td width="12%">Total</td>
				<td width="12%">{{ format_number(Cartify::cart()->total()) }}</td>
			</tr>
		</tbody>
	</table>
	<div class="pull-right">
		<a href="{{ URL::to('cartify') }}" class="btn">Continue Shopping</a>
		<a href="#" class="btn btn-info">Checkout</a>
	</div>

	<button type="submit" id="update" name="update" value="1" class="btn btn-success">Update</button>
	<button type="submit" id="empty" name="empty" value="1" class="btn btn-warning">Empty the Cart</button>
	@endif
</form>
@endsection
