<?php

class AttributeGroup extends Eloquent
{
    public static $table = 'attribute_groups';
    public $include = array('getAttributes');

    public function getAttributes()
    {
        return $this->has_many('Attribute', 'attribute_group_id');
    }

    public function getParentGroup()
    {//could be belongs to, may not even needed *UPDATE : it is needed for getting subs.
        return $this->has_many('AttributeGroup', 'parent_id');
    }

    public function getChildrenGroup()
    {
        return $this->has_many('AttributeGroup', 'parent_id');
    }

    public function getTopGroup()
    {
        return $this->belongs_to('AttributeGroup', 'parent_id');
    }
}