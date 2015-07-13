@layout('layouts.default')

@section('content')
<div class="container-fluid">
    <div class="page-header">
    <h1>Üye Giriş <small>Alışveriş keyfiniz kaldığı yerden devam ediyor!</small></h1>
    </div>
	<div class="row-fluid well" style="padding:0">
		<div class="span6">
				<form style="margin-top:50px" action="{{URL::base()}}/user/login" method="POST" class="form-horizontal">
					@if (Session::has('login_errors'))
					<div class="alert alert-error">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>Hata!</strong> Girmiş olduğunuz e-posta veya şifre hatalıdır.
					</div>
					@endif
					<div class="control-group">
						<label class="control-label" for="inputEmail">E-Posta Adresiniz</label>
						<div class="controls">
							<input type="text" name="inputMail" id="inputEmail" placeholder="E-Posta">
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputPassword">Şifreniz</label>
						<div class="controls">
							<input type="password" name="inputPassword" id="inputPassword" placeholder="Şifre">
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<label class="checkbox">
								<input name="inputRememberMe" value="1" type="checkbox"> Beni Hatırla
							</label>
							<button type="submit" class="btn">Giriş Yap</button>
						</div>
					</div>
				</form>
		</div>
		<div class="span6">
		<div class="login-page">
		</div>
		</div>
	</div>
</div>

@endsection