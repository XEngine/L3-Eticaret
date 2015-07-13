<div id="header">
	<div class="top-elements">
		<a class="logo logoLeft" href="{{URL::base()}}">
			@if(Sentry::user()->in_group(3))
			<script type="text/javascript">
				<?php $accepted = Session::has('Agreement_Bayi'); ?>
					<?php if(!$accepted){ ?>
					$(document).ready(function(){
					    $.fn.SimpleModal({
					    	btn_ok: 'ANLADIM',
					    	title: 'BAŞLIK',
					    	callback: function(){
					            <?php Session::put('Agreement_Bayi',''); ?>
					        },
					    	contents: "BeyazEşyaPazar.com'un ayrıcalıklı dünyasına hoş geldiniz! bla bla. bla..."
					    }).showModal();
					})
					<?php } ?>
			</script>
				<img src="{{URL::base()}}/img/bep-bayi-logo.png" alt="KarelGroup DTM">
			@else
				<img src="{{URL::base()}}/img/logo.png" alt="KarelGroup DTM">
			@endif
		</a>

		<div id="topMenu">
            <div class="topNavBar clearfix">
				@if(Sentry::user()->in_group(3))
            	<a rel="nofollow" href="{{URL::base()}}/bayi/payment"><i class="fa fa-credit-card"></i><b> Ödeme Yap</b></a>
            	@endif
				<a rel="nofollow" href="{{URL::base()}}/contact">İletişim</a>
                <a rel="nofollow" href="{{URL::base()}}/user/help-desk">Yardım</a>
				@if (Sentry::check())
				<a rel="nofollow" href="{{URL::base()}}/user/logout">Çıkış</a>
				<a rel="nofollow" href="{{URL::base()}}/user/dashboard">Hoşgeldiniz, <strong>{{Sentry::user()->metadata['first_name']}} {{Sentry::user()->metadata['last_name']}}</strong></a>
				@else
                <a rel="nofollow" href="{{URL::base()}}/user/login">Giriş Yap</a>
                <a rel="nofollow" href="{{URL::base()}}/bayi/login">Bayi Girişi</a>
                <a rel="nofollow" class="bold" href="{{URL::base()}}/user/register">Üye Ol</a>
				@endif
            </div>
        </div>
		<div style="position:absolute;bottom:55px;right:0;">
			<img src="{{URL::base()}}/img/guvence.png" alt="beyazesyapazar.com Güvence!" />
		</div>
		<div id="nav">
			<div id="categoryMenu">
				<ul class="navigation-main unstyled">
					<li>
						<h3><i class="fa fa-arrow-circle-down"></i> | TÜM KATEGORİLER</h3>
						<div class="overlay-nav-main">
							<div id="all-cat">
				                <ol>
				                	@foreach($cat as $item)
					                    <li>
					                        <h2><span>{{$item->getDescriptions->name}}</span></h2>
					                        <div>
					                        	<div class="container-fluid">
					                        		<div class="row-fluid">
					                        			<div class="span5">
					                        				<ul class="unstyled main-cat">
					                        				@foreach($item->getChildren as $child)
   																<li><i class="fa fa-angle-right"></i> <a href="{{URL::base()}}/category/{{$child->getDescriptions->alias}}">{{$child->getDescriptions->name}}</a></li>
					                        				@endforeach
															</ul>
					                        			</div>
					                        			<div class="span7">
					                        				<div class="nav-dropdown-right">
																<div class="menu_img_effect"></div>
																<a href="">
																	<img src="https://www.beyazesyapazar.com/img/advert_beyaz.png" alt="Beyaz Eşya ">
																</a>
															</div>
					                        			</div>
				                        			</div>
				                        		</div>
					                        </div>
					                    </li>
									@endforeach
				                </ol>
				            </div>
						</div>
					</li>
				</ul>
			</div>
		<div class="nav-rightpart">
				<div id="searchbox">
					<form method="GET" action="#" id="productSearchForm">
						<input type="text" autocomplete="off" class="empty" maxlength="255" value="" placeholder="Ürünlerimiz arasında arama yapın" id="productSearchInput">
						<input type="hidden" value="" name="categoryGroup" id="categoryGroup">
						<input type="hidden" value="" name="q" id="q">
						<input type="hidden" value="" name="seller" id="seller">
						<div class="search">
							<button class="button medium black" type="button" id="btnSearch">
								ARA
							</button>
						</div>
					</form>
				</div>

				<div class="dropcart-link">
					<a class="cart" id="dropcart" href="javascript:void(0);"><i class="fa fa-shopping-cart"></i> Alışveriş Sepeti</a>
					<div class="dropcart-panel"></div>
				</div>
			</div>
		</div>
	
	</div>
</div>