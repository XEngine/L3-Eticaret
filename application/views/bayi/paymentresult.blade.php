@layout('layouts.default')

@section('content')
<div class="container-fluid">
    <div class="page-header">
    <h1>Ödemeniz Tamamlandı <small></small></h1>
    </div>
	<div class="row-fluid">
		<div class="span12">
			    <div class="hero-unit">
			  		@if($response->isSuccess())
				  	    <h1>Ödeme Başarılı!</h1>
				    	<p>Tebrikler, ödemenizi başarıyla tamamladınız!</p>
				    @else
				  	    <h1>Ödemenizde Sorun Var!</h1>
				    	<p>Ödemenizle ilgili bir hata oluştu lütfen tekrar deneyiniz.</p>
				    @endif
				    <p>
				    	@if($response->isSuccess())
				    		<a href="https://www.beyazesyapazar.com" class="btn btn-success btn-large">
				   				Siteye Geri Dön
				   			</a>
				   		@else{
				    		<a href="https://www.beyazesyapazar.com" class="btn btn-warning btn-large">
				   				Ödeme Ekranına Dön
				   			</a>
				   		}
				    </p>
			    </div>
		</div>
	</div>
</div>
@endsection