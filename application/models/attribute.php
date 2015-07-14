<?php

class Attribute extends Eloquent
{
    public $includes = array('fetchGroup');

    /**
     * undocumented function
     *
     * @return array
     **/
    public function fetchGroup()
    {
        return $this->belongs_to('AttributeGroup', 'attribute_group_id');
    }

    public function productSpecific()
    {
        return $this->has_many_and_belongs_to('Product', 'map_product_attribute');
    }

}