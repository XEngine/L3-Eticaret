@layout('layouts.default')

@section('content')
<script type="text/javascript">
$(document).ready(function(){
	var latlng = new google.maps.LatLng(38.403121973784856, 27.124691605567932); 
	var marker;
    var myOptions = { 
      zoom: 17, 
	  disableDefaultUI: true,
      center: latlng, 
	  draggable: false,
	  zoomControl: false,
	  scrollwheel: false,
	  disableDoubleClickZoom: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP 
    }; 
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	marker = new google.maps.Marker({
		map:map,
		draggable:true,
		animation: google.maps.Animation.DROP,
		position: latlng
	});
});
</script>
<div class="map_dummy">
	<div class="map_info">
	    <address>
			<strong>KarelGroup DTM</strong><br>
			Saim Çıkrıkçı Caddesi, No: 5/D, Güneşli Mahallesi<br>
			Konak / İzmir<br>
			<abbr title="Ofis Telefonu">Telefon:</abbr> (123) 456-7890
			</address>
			 
			<address>
			<strong>E-Posta Adresi</strong><br>
			<a href="mailto:destek@karelgroup.com">destek@karelgroup.com</a>
			</address>
	</div>
</div>
<div id="map_canvas" class="contact-map">
</div>
@endsection