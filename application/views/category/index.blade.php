@layout('layouts.default')

@section('content')
@if($result->top != 1)
<div class="row-fluid" id='categoryPage'>
	<div class="row-fluid">
		<div class="span12">
			<div id="breadcrumb">
			<ul class="breadcrumb" style="margin-bottom: 5px;">
			<li>
				<a href="{{URL::base()}}">Ana Sayfa</a>
				<span class="divider">/</span>
			</li>
		    <li class="dropdown">
			    <ul id="branches-dropdown" role="menu" class="dropdown-menu">
			    	@foreach($result->getChildren as $item)
			        <li><a href="{{URL::base()}}/category/{{$item->getDescriptions->alias}}"><i class="fa fa-circle"></i> {{$item->getDescriptions->name}} <code>{{count($item->getProducts)}}</code></a></li>
			        @endforeach
			    </ul>
		   		<span class="divider">/</span>
		    </li>
			<li class="active">{{$result->getDescriptions->name}}</li>
			</ul>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span3">
			<div class="menu clearfix">
				<div class="arrow_box"></div>
				<div class="menu_box"><p class="menu_box_par">Sonuçları Filtrele</p></div>
			</div>
			<div class="filter">
                <?php if($filters != null) { ?>
                <div class="box">
                    <ul class="nav nav-list">
                        <li class="nav-header" style="color:#b80000">Seçilen Filtreleriniz</li>
                        <div class="filter-max-height">
                            @foreach($filters as $k => $v)
                                <li><a class="attribute-link" data-attr-class="{{$k}}" data-value="{{$v['val']}}" href="">
                                            <img src="{{URL::base()}}/img/icons/cancel.png">
                                            <b style="color:#b80000">{{$k}} : </b>
                                            <i>{{$v['val']}}</i>
                                    </a>
                                </li>
                            @endforeach
                        </div>
                    </ul>
                </div>
                <?php } ?>
				<div class="box">
					<ul class="nav nav-list">
						<li class="nav-header">Fiyat Aralığı</li>
						<div class="filter-max-height">
						<?php if(!empty($selected_ranges)){
							$checked = "checked=''";
							$style = "style=\"color:#b80000\"";
						}else{
							$checked = '';
							$style= '';
						}
						?> 
						<?php foreach($ranges as $range) { ?>
							@if(isset($range['values']))
							<li>
								<label class="checkbox">
									<input type="checkbox" class="attribute-checkbox" {{$checked}} data-type="1" data-attr-class="Price Range" data-value="{{$range['str']}}"><span {{$style}}>{{$range['str']}} ({{$range['count']}})</span>
								</label>
							</li>
							@endif
						<?php } ?>
						</div>
					</ul>
				</div>
				@foreach($result->getAttributeListing as $item)
					<div class="box">
						<ul class="nav nav-list">
							<?php
								if($filters != null && array_key_exists($item->name,$filters)){
									$navStyle = "style=\"color:#b80000\"";
								}else{ $navStyle = ""; }
							?>
							<li {{$navStyle}} class="nav-header">{{$item->name}}</li>
							<div class="filter-max-height">
							<?php foreach($item->getAttributes as $attributeItem) { ?>
								<?php
									if(isset($filters[$item->name]) && $attributeItem->id == $filters[$item->name]['id']){
										$lblStyle = "style=\"color:#b80000\"";
										$check = "checked=\"checked\"";
									}else{ $lblStyle = ""; $check=""; }
								?>
								@if(array_key_exists($attributeItem->id,$attribute_filter))
								<li>
									<label class="checkbox">
										<input type="checkbox" {{$check}} class="attribute-checkbox" data-type="0" data-attr-class="{{$item->name}}" data-value="{{$attributeItem->value}}"><span {{$lblStyle}} >{{$attributeItem->value}} ({{$attribute_filter[$attributeItem->id]}})</span>
									</label>
								</li>
								@endif
							<?php } ?>
							</div>
						</ul>
					</div>
				@endforeach
			</div>
		</div>
		<div class="span9">
			<div class="navbar navbar-googlenav">
				<div class="navbar-inner">
					<ul class="nav">
						<li><a href="#"><i class="icon-th"></i></a></li>
						<li><a href="#"><i class="icon-th-list"></i></a></li>
					</ul>
					
					<ul class="nav">
						<li><a href="#">Çok Satılana Göre</a></li>
						<li><a href="#">Puanına Göre</a></li>
						<li><a href="#">Fiyatına Göre</a></li>
					</ul>
				</div>
			</div>
	        <div id="productList">
	            @render('category.product_list', array('result' => $products,'filters'=>$filters))
	        </div>
		</div>
	</div>
</div>
@else
<div id="breadcrumb">
	<ul class="breadcrumb" style="margin-bottom: 5px;">
	<li>
		<a href="{{URL::base()}}">Ana Sayfa</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		{{$result->getDescriptions->name}}
		<span class="divider">/</span>
	</li>
    <li class="dropdown">
	    <a class="dropdown-toggle" id="branches" role="button" data-toggle="dropdown" href="#">Alt Kategoriler <b class="caret"></b></a>
	    <ul id="branches-dropdown" role="menu" class="dropdown-menu">
	    	@foreach($result->getChildren as $item)
	        <li><a href="{{URL::base()}}/category/{{$item->getDescriptions->alias}}"><small><i class="fa fa-minus"></i> {{$item->getDescriptions->name}}</small> <code>{{count($item->getProducts)}}</code></a></li>
	        @endforeach
	    </ul>
    </li>
	</ul>
</div>

<div class="row-fluid" id="CategoryMenu">
	<div class="categoryMenu span12">
		<div class="categoryMenuContainer clearfix">
			@foreach($cat as $item)
				<div class="categoryTab <?php echo ($result->id == $item->id) ?  "active" : ''; ?>">
					<p>
						<span class="icon"><img width="16" height="16" src="{{$item->getDescriptions->icon}}"/></span>
						<span class="title">{{$item->getDescriptions->name}}</span>
					</p>
					@if($item->getChildren)
						<div class="categoryMenuDropDown">
							@foreach($item->getChildren as $child)
								<dt><i class="fa fa-angle-right"></i><a href="{{URL::Base()}}/category/{{$child->getDescriptions->alias}}"> {{$child->getDescriptions->name}}</a></dt>
							@endforeach
						</div>
					@endif
				</div>
			@endforeach
		</div>
	</div>
</div>
<div class="row-fluid" id="NewsAndSlider">
	<div class="span3">
	@render('category.list_by_category', array('var' => $result))
	</div>
	<div class="span9">
	@if(!empty($result->getSlideshow))
			@foreach($result->getSlideshow as $item)
				@if($item->main == 1)
				<ul id="slider">
					@foreach($item->getItems as $child)
						@if(!empty($child->item_url))
							<li><a href="{{$child->item_url}}"><img src="{{$child->item_img}}" alt="" /></a></li>
						@else
							<li><img src="{{$child->item_img}}" alt="" /></li>
						@endif
					@endforeach
				</ul>
				@endif
			@endforeach
	@endif
	</div>
</div>
<div class="row-fluid theBlock">
	<div class="span12">
		@render('blocks.topselledproducts', array('var' => $result->id))
	</div>
</div>
<div class="row-fluid theBlock mostViewedProducts">
	<div class="span8">
		@render('blocks.mostviewedproducts', array('var' => $result->id))
	</div>
	<div class="span4">
		<img src="/img/slider/leaderboard1.jpg" />
	</div>
</div>
<script>
$(document).ready(function() {
	$('#slider').rhinoslider({
		controlsMousewheel: false,
		controlsKeyboard: false,
		controlsPrevNext: true,
		controlsPlayPause: false,
		showBullets: 'always',
		showControls: 'always'
	});
});
</script>
@endif
@endsection