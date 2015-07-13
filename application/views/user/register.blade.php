@layout('layouts.default')

@section('content')
<form action="/user/register" class="form-horizontal" method="POST" data-validate="parsley">
<div class="container-fluid">
    <div class="page-header">
    <h1>Üye Kayıt <small>Alışveriş keyfiniz sadece bir tık ilerde!</small></h1>
    </div>
	<div class="row-fluid">
		<div class="span6">
			<!--Ad-->
					{{-- Registeration Errors --}}
			@if($errors->has())
				@foreach ($errors->all() as $error)
					<div>{{ $error }}</div>
				@endforeach
			@endif
			 <div class="control-group">
				<label class="control-label" for="inputName">Adınız :</label>
				<div class="controls">
					<input id="inputName" tabindex="1" data-required="true" name="regName" type="text" placeholder="Adınız">
				</div>
			</div>
			<!--Mail-->
			 <div class="control-group">
				<label class="control-label" for="inputMail">E-Posta Adresiniz :</label>
				<div class="controls">
					<input type="text" tabindex="3" data-required="true" id="inputMail" name="regMail" placeholder="E-Posta Adresiniz" data-type="email" />
				</div>
			</div>
			<!--Şifre-->
			 <div class="control-group">
				<label class="control-label" for="inputPassword">Şifreniz :</label>
				<div class="controls">
					<input name="regPassword" tabindex="5" data-required="true" data-rangelength="[6,18]" id="inputPassword" type="password" placeholder="Şifreniz" />
					<div class="passwordStr clearfix">
						<div class="progress progress-striped active pull-left">
							<div class="bar" style="width: 0;"></div>
						</div>
						<div id="passwordStrBlock">
						
						</div>
					</div>
				</div>
			</div>
			<!--End-->
		</div>
		<div class="span6">
			<!--Soyadı-->
			 <div class="control-group">
				<label class="control-label" for="inputLastname">Soyadınız :</label>
				<div class="controls">
					<input id="inputLastname" tabindex="2" data-required="true" name="regLastName" type="text" placeholder="Soyadınız">
				</div>
			</div>
			<!--Mail Tekrar-->
			 <div class="control-group">
				<label class="control-label" for="inputMail2">E-Posta Adresiniz(tekrar) :</label>
				<div class="controls">
					<input type="text" tabindex="4" data-required="true" id="inputMail2" name="regMail2" placeholder="E-Posta Adresiniz(tekrar)" data-equalto="#inputMail" />
				</div>
			</div>
			<!--Şifre Tekrar-->
			 <div class="control-group">
				<label class="control-label" for="inputPassword2">Şifreniz(tekrar) :</label>
				<div class="controls">
					<input name="regPassword" tabindex="6" data-required="true" data-equalto="#inputPassword" id="inputPassword2" type="password" placeholder="Şifreniz" />
				</div>
			</div>
			<!--End-->
		</div>
	</div>
	<hr />
	<div class="row-fluid">
		<div class="span6">
			<!--Doğum Tarihi-->
			<div class="control-group">
				<label class="control-label" for="inputDate">Doğum Tarihiniz :</label>
				<div class="controls">
					<input type="text" tabindex="7" id="inputDate" data-required="true" data-date-format="dd/mm/yyyy" value="01/01/1995" name="regDate">
				</div>
			</div>
			<!--Şehir-->
			<div class="control-group">
				<label class="control-label" for="inputCity">Şehir :</label>
				<div class="controls">
					<select id="inputCity" tabindex="10" name="regCity" onchange="getTowns(this)">
						@foreach($city as $item)
							<option value="{{$item->id}}">{{$item->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<!--Cep Telefonu-->
			<div class="control-group">
				<label class="control-label" for="inputPhone">Telefonunuz :</label>
				<div class="controls">
					<input class="bfh-phone" tabindex="12" id="inputPhone" name="regPhone" type="text" data-country="TR">
				</div>
			</div>
		</div>
		<div class="span6">
			<!--Cinsiyet-->
			<div class="control-group">
				<label class="control-label" tabindex="8" for="inputWoman">Cinsiyet :</label>
				<div class="controls">
					<label for="inputWoman" class="radio inline">
						<input type="radio" data-required="true" id="inputWoman" name="regGender" value="Woman"> Kadın
					</label>
					<label for="inputMan" class="radio inline">
						<input type="radio" tabindex="9" id="inputMan" name="regGender" value="Man"> Erkek
					</label>
				</div>
			</div>
			<!--Semt-->
			<div class="control-group">
				<label class="control-label" for="towns">Semt :</label>
				<div class="controls">
					<select name="regTown" tabindex="11" id="towns">
						@foreach($towns as $item)
							<option value="{{$item->id}}">{{$item->name}}</option>
						@endforeach
					</select>
				</div>
			</div>
			<!--Cep Telefonu-->
			<div class="control-group">
				<label class="control-label" for="inputCell">Cep Telefonunuz :</label>
				<div class="controls">
					<input data-required="true" tabindex="13" class="bfh-phone" id="inputCell" name="regCell" type="text" data-country="TR">
				</div>
			</div>
			
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
		<blockquote>
			<label class="checkbox">
				<input data-required="true" tabindex="14" name="regAgreement" type="checkbox"> Üyelik sözleşmesini okudum, kabul ediyorum. 
			</label>
			<small><a href="javascript:void(0)" tabindex="15" onclick="showAgreement(this)">Sözleşmeyi görüntülemek için tıklayınız</a></small>
		</blockquote>
		</div>
		<button class="btn btn-large btn-primary pull-right" tabindex="16" type="submit">KAYIT OL!</button>
	</div>
</div>
<script>
$('#inputDate').datepicker();
</script>
@endsection