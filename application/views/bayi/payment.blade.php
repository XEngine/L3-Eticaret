@layout('layouts.default')

@section('content')
<div class="container-fluid">
    <div class="page-header">
    <h1>Cari Ödeme <small>Cari ödemelerinizi buradan yapabilirsiniz!</small></h1>
    </div>
	<div class="row-fluid">
		<div class="span12">
			<blockquote>
				<p>Merhaba, <strong>{{Sentry::user()->metadata['first_name']}} {{Sentry::user()->metadata['last_name']}}!</strong></p>
			</blockquote>
		</div>
		<div class="row-fluid">
			<div class="span7">
		    	<form action="/bayi/payment/" method="POST" class="form-horizontal span12">
			        <fieldset>
			          <legend>Kart Bilgileri</legend>
			       
			          <div class="control-group">
			            <label class="control-label">Kart Sahibinin Adı / Soyadı</label>
			            <div class="controls">
			              <input type="text" class="input-block-level" name="owner" pattern="\w+ \w+.*" title="Fill your first and last name" required>
			            </div>
			          </div>
			       
			          <div class="control-group">
			            <label class="control-label">Kart Numarası</label>
			            <div class="controls">
			              <div class="row-fluid">
			                <div class="span12">
			                  <input type="text" class="input-block-level" id="cc" name="cc" autocomplete="off" maxlength="16" pattern="\d{16}" required>
			                </div>
			              </div>
			            </div>
			          </div>
			       
			          <div class="control-group">
			            <label class="control-label">Kart Son Kullanım Tarihi</label>
			            <div class="controls">
			              <div class="row-fluid">
			                <div class="span3">
			                  <input class="input-block-level"maxlength="2" name="mm" placeholder="07" type="text" pattern="\d{2}">
			                </div>
			                <div class="span3">
			                  <input class="input-block-level" maxlength="2" name="yy" placeholder="14" type="text" pattern="\d{2}">
			                </div>
			              </div>
			            </div>
			          </div>
			       
			          <div class="control-group">
			            <label class="control-label">Kart Güvenlik Kodu</label>
			            <div class="controls">
			              <div class="row-fluid">
			                <div class="span3">
			                  <input type="text" class="input-block-level" name="cvv" autocomplete="off" maxlength="3" pattern="\d{3}" title="Three digits at back of your card" required>
			                </div>
			                <div class="span8">
			                  <!-- screenshot may be here -->
			                </div>
			              </div>
			            </div>
			          </div>
			          <div class="control-group">
			            <label class="control-label"><b>Ödenecek Tutar</b></label>
			            <div class="controls">
			                <div class="input-append">
			                  <input class="span12" id="appendedInput" name="price" type="text">
							  <span class="add-on">.00 TL</span>
			                </div>
			            </div>
			          </div>
			       
			          <div class="form-actions">
			          	<input type="hidden" value="" name="bnkinfo" id="bnkinfo">
			            <button type="submit" class="btn btn-primary">Gönder</button>
			          </div>
			        </fieldset>
		      	</form>
      		</div>
      		<div class="span3" id="bankinginfo">

      		</div>
      		<div class="span2">
      			<p class="lead">Cari Bilgileriniz</p>
  			    <dl>
			    	<dt>Cari Borcunuz</dt>
			    	<dd>980.00TL</dd>
			    	<dt>Ödeme Geçmişiniz</dt>
			    	<dd>
			    		<ul>
				    		<li>340TL - 02.02.2014</li>
				    		<li>530TL - 12.03.2014</li>
				    		<li>845TL - 13.03.2014</li>
			    		</ul>
			    		<a href="/bayi/history"><small>Tüm geçmişi görüntüle...</small></a>
			    	</dd>
			    </dl>

      		</div>
      	</div>
	</div>
</div>
<script type="text/javascript">
function getBankInfo(e){

	$objVal= $(this).val();
	url = '/bayi/payment/getbank';
    $.ajax({
        type: "POST",
        url: url,
        data: 'cc=' + $objVal,
        dataType: "json",
        success: function(data) {
            $container = $("#bankinginfo");
            /*Fill visible input fields for posting*/
            $("#bnkinfo").val(data.getBankInformation.factory);

            /*TO DO : Taksit bilgisi alınacak*/

            $html = "<img width='90px' src='"+data.getBankInformation.factory_logo+"'>";
            $container.html($html);
        }
    });

}
$(document).on("blur","#cc",getBankInfo);

</script>
@endsection