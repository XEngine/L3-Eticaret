<?php

class User extends Eloquent {
	public static $table = 'users';

	public function getAddress(){
		return $this->has_many('Address');
	}
}