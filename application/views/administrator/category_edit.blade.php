@layout('layouts.administrator')

@section('content')
<div class="container-fluid">
	<!-- BEGIN PAGE HEADER-->
	<div class="row-fluid">
		<div class="span12">
			<h3 class="page-title">
				Kategori Yönetimi<small>	Kategori Düzenleme</small>
			</h3>
		</div>
	</div>
	<!-- END PAGE HEADER-->
	<!-- BEGIN PAGE CONTENT-->
	<div class="row-fluid">
		<div class="span6">
		    <form>
			    <fieldset>
				    <legend>{{$result->getDescriptions->name}} Kategorisi Düzenleme</legend>

				    <label>Kategori Adı</label>
				    <input type="text" value='{{$result->getDescriptions->name}}'>

				    <label>Kategori Açıklaması</label>
				    <textarea rows="3">{{$result->getDescriptions->description}}</textarea>

				    <span class="help-block">Eğer bu kategori başka bir kategorinin alt kategorisi değilse boş bırakınız</span>
				    <label>Üst Kategori</label>
				    <select tabindex="1" style="width:350px;" class="chzn-select" data-placeholder="Bir üst kategori seçiniz">
				    	<option value=''></option>
				    	<?php foreach($all as $item){ ?>
				    		<?php if($result->getTopCat && $result->getTopCat->id == $item->id){ ?>
								<option selected='selected' value="<?php echo $item->id ?>"><?php echo $item->getDescriptions->name ?></option>
							<?php } ?>
							<option value="<?php echo $item->id ?>"><?php echo $item->getDescriptions->name ?></option>
						<?php } ?>
					</select>
					<label>Kategori SEO Tagleri</label>
					<input id="tag" type="hidden" value="{{$result->getDescriptions->meta_keywords}}">
					<ul  id="allowSpacesTags"></ul>
					<br>
					<label>Kategori Resmi</label>
					{{Form::file('image')}}
					<script type="text/javascript">
						jQuery('#allowSpacesTags').tagit({
							allowSpaces: true,
							singleField: true,
							singleFieldNode: $('#tag')
						}); 
					</script>
					<script type="text/javascript">$(".chzn-select").chosen({no_results_text: "No results matched",allow_single_deselect:true}); </script>
					<br /><br />
				    <button type="submit" class="btn">Kaydet</button>
			    </fieldset>
		    </form>
		</div>
		<div class="span6">
			
		</div>
	</div>
</div>

@endsection