@layout('layouts.default')

@section('content')

{{-- Getting Images --}}
<!-- TODO: fiyatlara cache ekle ürün başına -->
<?php $productObj = $product->getProducts[0]; ?>
<?php $images = getProductImages($productObj) ?>
<?php $price = getItemPrice($productObj) ?>
{{-- Getting Images --}}
<div class="row-fluid">
	<div class="span12">
		<div id="breadcrumb">
			<ul class="breadcrumb" style="margin-bottom: 5px;">
			<li>
				<a href="{{URL::base()}}">Ana Sayfa</a>
				<span class="divider">/</span>
			</li>
			<li>
				Ana Kategori : <a href="{{URL::base()}}/category/{{$product->getTopCat->getDescriptions->alias}}">{{$product->getTopCat->getDescriptions->name}}</a>
				<span class="divider">/</span>
			</li>
			<li>
				Kategori : <a href="{{URL::base()}}/category/{{$product->getDescriptions->alias}}">{{$product->getDescriptions->name}}</a>
				<span class="divider">/</span>
			</li>
			<li class="active">{{$productObj->getDetail->name}}</li>
			</ul>
		</div>
	</div>
</div>
<div class="row-fluid">
		<div class="span6">
			@if($images != null)
				<div class="mainimg">
					<a href="<?php echo $images->main->huge ?>" class="cloud-zoom" rel="transparentImage: 'data:image/gif;base64,R0lGODlhAQABAID/AMDAwAAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==', useWrapper: true, showTitle: false">
					   <img src = "<?php echo $images->main->large ?>" />
					</a>
				</div>
				<div class="thumbs">
					<ul class="clearfix">
						@foreach($images->images as $item)
							<li>
							    <img data-huge="<?php echo $item->huge ?>" data-thumb="<?php echo $item->medium ?>" src="<?php echo $item->tiny ?>" />
							</li>
						@endforeach
					</ul>
				</div>
			@endif
		</div>
		<div class="span6">
			<div class="product-header"><h1>{{$productObj->getDetail->name.' ('.$productObj->getDetail->variant.')'}}</h1></div>
			<div class="panels">
				<div class="no-rating">
					<a href="#tab2" data-toggle="tab">Bu ürüne yorum yazan ilk siz olun!</a>
				</div>
				<div class="stock_box"> 
		    		<p class="availability in-stock">Stok durumu: <span><i class="fa fa-check-circle"></i> Stokta var!</span></p>
	            </div>
			</div>
			<div class="panels">
				<div class="short-description">
                    <div class="std">{{$productObj->getDetail->variant}}</div>
                </div>
            </div>
			<div class="panels clearfix">
				<div class="price-new pull-left">
					<span class="a">{{$price->get_taxed}}</span>
					<span class="b">KDV DAHİL</span>
				</div>
				<div class="add-to-cart pull-right">
	                <label for="qty">Adet:</label>
	        			<div class="qty_pan">
       					<input type="text" class="input-text qty validation-passed" title="Qty" value="1" maxlength="12" id="qty" name="qty">
	        			<div class="add" onclick="CartInputModifier()">+</div>
	        			<div onclick="CartInputModifier_Dec()" class="dec add">-</div></div>
	                	<button onclick="addtoCart(<?php echo $productObj->id ?>)" class="button btn-cart validation-passed" title="Add to Cart" type="button"><span><span>Sepete Ekle</span></span></button>
	            </div>
			</div>
			<div class="panels">
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style">
					<a class="addthis_button_preferred_1"></a>
					<a class="addthis_button_preferred_2"></a>
					<a class="addthis_button_preferred_3"></a>
					<a class="addthis_button_preferred_4"></a>
					<a class="addthis_button_compact"></a>
					<a class="addthis_counter addthis_bubble_style"></a>
				</div>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52c8738a533e9719"></script>
				<!-- AddThis Button END -->
			</div>
		</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<div style="margin-bottom: 18px;" class="tabbable">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#tab1">Ürün Özellikleri</a></li>
                <li class=""><a data-toggle="tab" href="#tab2">Yorumlar</a></li>
                <li class=""><a data-toggle="tab" href="#tab3">Taksit Seçenekleri</a></li>
        	</ul>
            <div style="padding-bottom: 9px; border-bottom: 1px solid #ddd;" class="tab-content">
                <div id="tab1" class="tab-pane active">
                	<?php foreach($attrs as $item){ ?>
                		<h6>{{$item->name}}</h6>
                		<?php foreach($item->getParentGroup as $item2){ ?>
	                		<dl class="dl-horizontal">
		                		<dt>{{$item2->name}} :</dt>
		                		<?php foreach($item2->getAttributes as $item3){ ?>
		                		<dd>{{$item3->value}}</dd>
		                		<?php } ?>
	                		</dl>
	                	<?php } ?>	
                	<?php } ?>
                </div>
                <div id="tab2" class="tab-pane">
                	@foreach($productObj->getComments as $item)
                	<div class="entryinsider">
                		<div class="inside">
                			<div class="entry clearfix">
	                			<div class="entrynumber">1</div>
	                			<div class="entry_text">{{$item->comment}}</div>
	                		</div>	
	                		<div class="edate" align="right">
	                			<span class="entry_user">Mert Kırşallıoba</span>
	                			<span class="entry_date">{{$item->created_at}}</span>
	                		</div>
                		</div>
                	</div>
                	@endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection