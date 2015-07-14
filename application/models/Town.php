<?php

class Town extends Eloquent
{
    public static $table = 'towns';

    public function getCity()
    {
        $this->belongs_to('City');
    }
}