<?php

class Binlist extends Eloquent {

	public function getBankInformation(){
		return $this->belongs_to('Bank','banka_kodu');
	}
	
}
