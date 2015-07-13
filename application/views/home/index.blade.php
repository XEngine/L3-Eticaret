@layout('layouts.default')

@section('content')
<div class="row-fluid">
	<div class="span3">
        @render('category.main_page_list')
    </div>
    <div class="span9">
		<div class="slider-box" style="width:700px;height:300px;">
			<!--LayerSlider layer-->
			<div class="ls-layer" style="transition3d: all; slidedelay: 6000">
		 
				<!--LayerSlider background-->
				<img class="ls-bg" src="{{URL::base()}}/img/slider/vantilator.jpg" alt="layer1-background">
			
			</div>
			<div class="ls-layer" style="transition3d: all; slidedelay: 6000">
		 
				<!--LayerSlider background-->
				<img class="ls-bg" src="{{URL::base()}}/img/slider/belderia.jpg" alt="layer1-background">
			
			</div>
			<div class="ls-layer" style="transition3d: all; slidedelay: 6000">
		 
				<!--LayerSlider background-->
				<img class="ls-bg" src="{{URL::base()}}/img/slider/lg.jpg" alt="layer1-background">
			
			</div>
			<div class="ls-layer" style="transition3d: all; slidedelay: 6000">
		 
				<!--LayerSlider background-->
				<img class="ls-bg" src="{{URL::base()}}/img/slider/sebil.jpg" alt="layer1-background">
			
			</div>
		</div>
    </div>
</div>
<div class="row-fluid theBlock">
	<div class="span12">
		@render('blocks.triplebanner')
	</div>
</div>
<div class="row-fluid theBlock">
	<div class="span12">
		@render('blocks.topselledproducts', array('var' => "all"))
	</div>
</div>
<div class="row-fluid theBlock">
	<div class="span12">
		@render('blocks.mostviewedproducts4sq', array('var' => "all"))
	</div>
</div>
@endsection