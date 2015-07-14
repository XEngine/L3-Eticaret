<?php

class City extends Eloquent
{
    public static $table = 'cities';

    public function getTowns()
    {
        return $this->has_many('Town');
    }
}