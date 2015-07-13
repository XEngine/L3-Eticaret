@layout('layouts.default')

@section('content')
<div class="container-fluid">
	<h2>Aktivasyon İşlemi</h2>
	@if($error)
	<p class="lead"><i class="icon-remove-circle"></i> Aktivasyon işleminiz başarısız oldu</p>
	<div class="row-fluid">
		<div class="span12">
		    <div class="well well-large">
			<h4 class="text-info"><i class="icon-meh"></i> Üzgünüz, bir hata oluştu!</h4>
			<p style="font-size:13px">Böyle bir üyelik bulunmamaktadır veya bu hesabı daha önce aktif etmiş olabilirsiniz.</p>
			<br>
			<a href="{{URL::Base()}}">Ana Sayfa</a>
			</div>
		</div>
	</div>
	@else
	<p class="lead"><i class="icon-thumbs-up-alt"></i> Aktivasyon işleminiz başarıyla gerçekleşti!</p>
	<div class="row-fluid">
		<div class="span12">
		    <div class="well well-large">
			<h4 class="text-info"><i class="icon-trophy"></i> Tebrik ederiz! Aktivasyon işlemini başarıyla gerçekleştirdiniz!</h4>
			<p style="font-size:13px">Mağazamız size bütün kapılarını açtı!<br> Hadi durma hemen gez, dolaş. Bu fiyatlarda, bu ürünler kaçmaz!</p>
			<br>
			<a href="{{URL::Base()}}">Ana Sayfa</a>
			</div>
		</div>
	</div>
	@endif
</div>
@endsection