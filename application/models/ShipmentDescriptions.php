<?php

class ShipmentDescription extends Eloquent {
	public static $table = 'shipment_descriptions';
	public function getShipment(){
		return $this->belongs_to('Shipment');
	}
}
