@layout('layouts.administrator')

@section('content')
<div class="container-fluid">
	<!-- BEGIN PAGE HEADER-->
	<div class="row-fluid">
		<div class="span12">
			<h3 class="page-title">
				Ürün Ekleme Yönetimi<small>	Ürün Ekleme</small>
			</h3>
		</div>
	</div>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="product-preview">
	<div class="barcode-pre"></div>
	</div>
	@if(Session::has("status"))
	<div class="row-fluid">
		<div class="span12">
			<div class="widget">
			  <div class="widget-header">
				<h5>Hata</h5>
			  </div>
			  <div class="widget-content">
			   
				<p class="text-error">{{Session::get("status")}}</p>
				
			  </div>
			</div>
		</div>
	</div>
	@endif
	@if(Session::has("result"))
	<div class="row-fluid">
          <div class="span12">
            <div class="widget">
              <div class="widget-header">
                <h5>Eklenen Ürün</h5>
              </div>
              <div class="widget-content">
                <dl class="dl-horizontal">

                  <dt>Ürün İsmi :</dt>
                  <dd>{{Session::get("result.name")}}</dd>

                  <dt>Ürün Fiyatı : </dt>
                  <dd>{{Session::get("result.price")}} TL - KDV</dd>
                  <dt>Özellikleri :</dt>
                  <?php $attrs = Session::get("result.attrs"); foreach($attrs[0] as $k => $v){ ?>
				  <dd>{{$v}}</dd>
				  <?php } ?>
                </dl>
              </div>
            </div>
          </div>
        </div>
	{{Session::forget("result")}}
	@endif
	<form action="#" method="post" class="form-horizontal" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="inputEmail"><i class="icon-chevron-right"></i> Ürün Kategorisi</label>
					<div class="controls">
						<select style="width:100%" name="p_category" id="e2">
							@foreach($categories as $category)
								<optgroup label="{{$category->getDescriptions->name}}">
									@foreach($category->getChildren as $subcat)
										<option value="{{$subcat->id}}">{{$subcat->getDescriptions->name}}</option>
										@if($subcat->getChildren)
											@foreach($subcat->getChildren as $deepSub)
												<option value="{{$deepSub->id}}"> |-- {{$deepSub->getDescriptions->name}}</option>
											@endforeach
										@endif
									@endforeach
								</optgroup>
							@endforeach
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Ürün Markası</label>
					<div class="controls">
						<select style="width:100%" name="p_brand" id="e1">
							@foreach($brands as $brand)
								<option value="{{$brand->id}}">{{$brand->name}}</option>
							@endforeach
						</select>

						<input type="text" name="p_brand_other" placeholder="Listede yoksa siz giriniz">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Ürün Modeli</label>
					<div class="controls">
						<input style="width:100%" id="p_model" name="p_model" type="text" placeholder="örn: DDL-220W">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Stok Adeti</label>
					<div class="controls">
						<input style="width:100%" name="p_qty" type="text" placeholder="örn: 78">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Fiyat</label>
					<div class="controls">
						<input style="width:100%" name="p_price" type="text" placeholder="örn: 7822,99">
					</div>
				</div>
								<div class="control-group">
					<label class="control-label" for="inputEmail">Vergi</label>
					<div class="controls">
						<select style="width:100%" name="p_tax">
							@foreach($taxes as $tax)
								<option value="{{$tax->id}}">{{$tax->title}} | {{$tax->rate}}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="control-group">
					<label class="control-label" for="inputEmail">SKU(Stok Kodu)</label>
					<div class="controls">
						<input style="width:100%" name="p_sku" id="sku" type="text" placeholder="örn: SK1239334">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Barkod</label>
					<div class="controls">
						<input style="width:100%" name="p_barcode" id="barcode" type="text" placeholder="örn: 8691234560013">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Ürün Kısa açıklaması</label>
					<div class="controls">
						<input style="width:100%" name="p_description" type="text" placeholder="örn: Bu ürün suda batmıyor!">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Detaylı açıklama</label>
					<div class="controls">
						<input style="width:100%" name="p_variant"  type="text" placeholder="örn: Intel Core i7 3630QM 2.4GHz 16GB 750GB+256GB SSD 17.3'' 3D Taşınabilir Bilgisayar">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"for="inputEmail">Tagler</label>
					<div class="controls">
						<input id="e20"  name="p_tag" type="text" style="width:100%">
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<div id="prodimages">
					<div class="row-fluid">
						<div id="imageinputs" class="span12">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Resim</th>
										<th>Ürün Ana Resmi</th>
										<th>Resim Ekle</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
											<input type="file" name="userfile[]"></td>
										<td  align="center">
											<input type="hidden" name="active[]" value="1" class="active" />
											<input type="radio" name="activeImage[]" checked="" onclick="$('.active').val(0);$(this).prev().val(1)" value="1" />
										</td>
										<td align="center">
											<div id="imageAdd" onclick="imageAdd()" style="font-size:20px;color:#00c0ff;cursor:pointer"><i class="fa fa-plus-square"></i></div>
										</td>
									</tr>
								</tbody>
							</table>
							
						</div>
						<div class="span1">
						
						</div>
					</div>
				</div>	
			</div>
			<div class="span6">
				<div id="attributes">
					
				</div>	
			</div>
		</div>
		<div class="send"> <button type="submit" class="btn">Submit</button></div>
	</form>
	
    <script>
	function TryBarcodeStok(){
		$('body').append('<div class="loading" id="overlay">Loading</div>');
		$.ajax({
			url:"/admin/getBarcode",
			type: 'POST',
			dataType: 'JSON',
			timeout:3000,
			data: "val="+$(this).val(),
			success:function(msg){
				$('body').find('#overlay').remove('#overlay');
				$('#barcode').val(msg.barcode);
				$('#sku').val(msg.stokcode);
			},
			error:function(err){
				$('body').find('#overlay').remove('#overlay');
			}
		});
	}
	$(document).on('blur','#p_model',TryBarcodeStok);
    $(document).ready(function() {
		var delay = (function(){
		  var timer = 0;
		  return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		  };
		})();
		$('#barcode').keyup(function() {
		delay(function(){
				$("#barcode").empty();
				$(".barcode-pre").barcode($("#barcode").val(), "ean13");
			}, 1000 );
		});
	
	  
		var tag = [<?php echo $tags; ?>];
		$("#e1").select2();
		$("#e2").select2();
		$("#e20").select2({
			tags: tag,
			tokenSeparators: [","]
		});
		var request;
		function get_attr(val){
			var request = $.ajax({
				url: "/admin/product/product_add_get_attribute",
				type: "POST",
				data: {id : val},
				dataType:'json'
			});

			request.success(function(data) {
				$container = $("#attributes");
				$container.empty();
				$html = '';
				$.each(data,function(index, item) {
					//Title definition.
					$html  += '<p class="lead">'+item.name+'</p>';
					$.each(item.getParentGroup,function(i, itemA) {
						$html += '<div class="control-group">';
						$html += '<label class="control-label" for="inputEmail">'+itemA.name+'</label>';
						$html += '<div class="controls">';
						$html += '<select name="attr['+itemA.id+']" style="width:100%" id="attr_'+itemA.id+'_'+i+'">';
					//	$html += '<select name="attr_'+itemA.id+'_'+i+'" style="width:100%" id="attr_'+itemA.id+'_'+i+'">';
						if(itemA.getAttributes.length > 0){
							$.each(itemA.getAttributes, function(i, itemB) {
								$html += '<option value="'+itemB.id+'">'+itemB.value+'</option>';
							});
						}else{
							$html += '<option value=""></option>';
						}
						$html += '</select>';
						$html += '<input name="attr_other['+itemA.id+']" type="text" placeholder="Listede yoksa siz giriniz">';
					//	$html += '<input name="attr_other_'+itemA.id+'" type="text" placeholder="Listede yoksa siz giriniz">';
						$html += '</div>';
						$html += '</div>';

					});
				});
				$($html).appendTo($container);
			});
		}
		get_attr($("#e2").select2("val"));
		$("#e2").on("change", function(e) { 
			get_attr(e.val);
		})
    });
	function imageAdd(){
		console.log("tıklandı");
		var $html = "<tr><td><input type='file' name='userfile[]'></td>";
			$html += "<td><input type='hidden' name='active[]' value='0' class='active' />"
			$html += "<input type='radio' name='activeImage[]' onclick=\"$('.active').val(0);$(this).prev().val(1)\" value='1' /></td>";
			$html += "<td><div id='imageAdd' onclick='imageAdd()' style='font-size:20px;color:#00c0ff;cursor:pointer'><i class='fa fa-plus-square'></i></div></td></tr>";
		$("#imageinputs table tr:last").after($html);
	}
</script>
@endsection