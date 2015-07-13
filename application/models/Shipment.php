<?php

class Shipment extends Eloquent {

	public function getDescriptions(){
		return $this->has_one('ShipmentDescription');
	}
	public function getProducts(){
		return $this->has_many_and_belongs_to('Product','map_product_shipment');
	}
}