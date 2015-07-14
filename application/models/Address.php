<?php

class Address extends Eloquent
{
    public static $hidden = array(
        'user_id',
        'id',
        'town_id',
        'city_id'
    );

    public function getCity()
    {
        return $this->belongs_to('City', 'city_id');
    }

    public function getTown()
    {
        return $this->belongs_to('Town', 'town_id');
    }

}
