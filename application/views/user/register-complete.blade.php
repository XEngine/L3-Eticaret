@layout('layouts.default')

@section('content')
<div class="container-fluid">
	<h2>Kayıt Başarılı</h2>
	<p class="lead">Kayıt işleminizi başarıyla gerçekleştirdiniz!</p>
	<div class="row-fluid">
		<div class="span12">
		    <div class="well well-large">
			<h4 class="text-info"><i class="icon-check"></i> Alışveriş keyfine son bir adım!</h4>
			<p style="font-size:13px">Üyelik işlemini tamamladınız! Bizi seçtiğiniz için <strong>teşekkür ederiz</strong>.<br>Alışveriş keyfinize son bir adımınız kaldı! Hemen e-postanızı kontrol edip, aktivasyon işlemini tamamlayınız.</p>
			<br>
			<a href="{{URL::Base()}}">Ana Sayfa</a>
			</div>
		</div>
	</div>
</div>
@endsection