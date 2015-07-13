<?php

class Slideshow extends Eloquent {
	public static $table = 'category_slideshows';
	public $includes = array('getItems');

	public function getItems(){
		return $this->has_many("Slideshowitems");
	}
}