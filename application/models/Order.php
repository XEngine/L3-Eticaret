<?php

class Order extends Eloquent {
	public static $timestamps = true;
	public $includes = array('getBuyer','getProduct','getShipment');
	
	public function getBuyer(){
		return $this->belongs_to('User','user_id');
	}
	public function getProduct(){
		return $this->belongs_to('Product','product_id');
	}
	public function getShipment(){
		return $this->belongs_to('Shipment','shipment_id');
	}
	public function get_shipmentStatus(){
		$status = $this->get_attribute('status');
		switch($status){
			case 0:
				return "İptal / İade / Değişim";
				break;
			case 1:
				return "Ürün tedarik ediliyor";
				break;
			case 2:
				return "Ürün paketleniyor";
				break;
			case 3:
				return "Kargo şirketine teslim edildi";
				break;
			case 4:
				return "Sipariş teslim edildi";
				break;
			default:
			   return "Beklenmedik bir hata meydana geldi";
		}
	}
	public function get_productQuantity(){
		return $this->get_attribute("quantity");
	}

}