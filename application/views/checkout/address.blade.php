@layout('layouts.default')

@section('content')
<div class="container">
    <div class="page-header">
    <h1>Alışverişi Tamamla <small>Adres Seçimi</small></h1>
    </div>
    <div class="row-fluid">
    	<div class="span12">
		    <div class="progress progress-success progress-striped">
		    	<div class="bar" style="width: 66.6%;"></div>
		    </div>
    	</div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Teslimat Adresi</th>
                        <th>Fatura Adresi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="span6">
                            <div>
                                <select class="span12" id="delivery">
                                    <option></option>
                                @foreach($addresses as $address)
                                    <option data-type="delivery" value="{{$address->id}}">{{$address->address_title}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div id="delivery-info" class="alert alert-info">
                                @if($defdelivery != null)
                                <strong>Seçili olan adresiniz:</strong><br>
                                {{$defdelivery->address}}
                                <p>{{$defdelivery->getTown->name}} / {{$defdelivery->getCity->name}}</p>
                                <p><small><b>FATURA TİPİ : </b>{{$defdelivery->bill_type}}</small>
                                | <small><b>TC KİMLİK NO : </b>{{$defdelivery->citizen_number}}</p>
                                @else
                                <strong>Dikkat!</strong> Lütfen bir adres seçiniz!
                                @endif
                            </div>
                        </td>
                        <td class="span6">
                            <div>
                                <select class="span12" id="billing">
                                    <option></option>
                                @foreach($addresses as $address)
                                    <option value="{{$address->id}}">{{$address->address_title}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div id="billing-info" class="alert alert-info">
                                @if($defbilling != null)
                                <strong>Seçili olan adresiniz:</strong><br>
                                {{$defbilling->address}}
                                <p>{{$defbilling->getTown->name}} / {{$defbilling->getCity->name}}</p>
                                <p><small><b>FATURA TİPİ : </b>{{$defbilling->bill_type}}</small>
                                | <small><b>TC KİMLİK NO : </b>{{$defbilling->citizen_number}}</p>
                                @else
                                <strong>Dikkat!</strong> Lütfen bir adres seçiniz!
                                @endif                            
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="pull-left">
                                <a class="btn btn-mini btn-danger">Yeni Adres Ekle</a>
                            </div>
                            <div class="pull-right">
                                <a href="/checkout/payment" class="btn btn-success">Adresi Onayla</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $("#delivery").select2({
        placeholder : "Lütfen Adres Seçiniz"
    });
    $("#billing").select2({
        placeholder : "Lütfen Adres Seçiniz"
    });
});
function setAddr(e){
    $.ajax({
        type: "POST",
        url: "/checkout/setAddress",
        datatype: "JSON",
        data: 'type=' + e.target.id + '&addrID=' + e.val,
        success: function(data) {
            var $html =  '<strong>Seçili olan adresiniz:</strong><br>'+data.address;
                $html += '<p>'+data.getTown.name+' / '+data.getCity.name+'</p>'
                $html += '<p><small><b>FATURA TİPİ : </b>'+data.bill_type+'</small>';
                $html += ' | <small><b>TC KİMLİK NO : </b>'+data.citizen_number+'</p>';
            var $container = $('#'+e.target.id+'-info');
            $container.html($html);
        },
    });
}
$("#delivery").on("change", setAddr);
$("#billing").on("change", setAddr);
</script>
@endsection