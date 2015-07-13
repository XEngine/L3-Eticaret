<div class="w570 bNone clearfix">
	<div class="productMarkaTopLeftt"></div>
	<div class="productMarkaTopCenter w550 Lh0 font0"></div>
	<div class="productMarkaTopRight Lh0 font0"></div>
	<div class="productMarkaContent w568 h272 colorBgWhite pull-left">
		<div class="clearfix w548 h272">
			<div class="pull-left w209 h272">
				<ul class="catMenuULCap font12">
					<li>{{HTML::link($categories[0]->getDescriptions->alias, 'Tüm Kategoriler')}}</li>
				</ul>
				<ul class="catMenuUL mt5">
					<li>
					@for($i = 1; $i < count($categories); $i++)
						{{HTML::link('/category/'.$categories[$i]->getDescriptions->alias, $categories[$i]->getDescriptions->name)}}
					@endfor
					</li>
				</ul>
			</div>
			<div class="pull-left h272 catMenuBg"></div>
			<div class="pull-left w332 h272 posR">
				<div class="pull-left w332 h90">
						@foreach($popular as $item)
							<div class="pull-left w110 h90 colorBgWhite posR">
								{{HTML::link('/category/'.$item->getDescriptions->alias,'',array('class'=>'privClassA'))}}
								<div class="pull-left w110 h58">
									{{HTML::image('img/category/'.$item->getDescriptions->alias.'.jpg', '')}}
								</div>
								<div class="pull-left w110 h32 proMenuCaps">{{$item->getDescriptions->name}}</div>
							</div>
						@endforeach
				</div>
			</div>
		</div>
	</div>
	<div class="productMarkaBottomLeft"></div>
	<div class="productMarkaBottomCenter w550"></div>
	<div class="productMarkaBottomRight"></div>
</div>