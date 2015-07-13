<?php

class CategoryDescription extends Eloquent {
	public static $table = 'category_descriptions';

	public function getCategory(){
		return $this->belongs_to('Category');
	}

}