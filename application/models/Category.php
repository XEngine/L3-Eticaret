<?php

class Category extends Eloquent
{
    public $includes = array(
        'getDescriptions',
        'getChildren',
        'getChildren.getDescriptions'
    );

    public function getDescriptions()
    {
        return $this->has_one('CategoryDescription');
    }

    public function getProducts()
    {
        return $this->has_many_and_belongs_to('Product', 'map_product_category');
    }

    public function getProductsOrdered()
    {
        return $this->has_many_and_belongs_to('Product', 'map_product_category')->order_by("products.ordered_qty", "desc")->take(4);
    }

    public function getProductsMostView()
    {
        return $this->has_many_and_belongs_to('Product', 'map_product_category')->order_by("products.views", "desc")->take(8);
    }

    public function getProductsMostView4sq()
    {
        return $this->has_many_and_belongs_to('Product', 'map_product_category')->order_by("products.views", "desc")->take(4);
    }

    public function getAttributeListing()
    {
        return $this->has_many_and_belongs_to('AttributeGroup', 'map_category_attribute_group');
    }

    public function getTopCat()
    {
        return $this->belongs_to('Category', 'parent_id');
    }

    public function getChildren()
    {
        return $this->has_many('Category', 'parent_id');
    }

    public function getSlideshow()
    {
        return $this->has_many_and_belongs_to('Slideshow', 'map_category_slideshow');
    }
}
