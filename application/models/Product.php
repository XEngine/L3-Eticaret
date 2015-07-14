<?php

class Product extends Eloquent
{
    public static $timestamps = false;
    public $includes = array(
        'getDetail',
        'getCategory',
        'getAttributes',
        'getAttributes.fetchGroup',
        'getAttributes.fetchGroup.getTopGroup',
        'getComments',
        'getImages',
        'getBrand',
        'getDiscount',
        "getTax"
    );

    /**
     * Get Product Categories
     *
     * @return array
     **/
    public function getCategory()
    {
        return $this->has_many_and_belongs_to("category", 'map_product_category');
    }

    /**
     * Get Product related Comments
     *
     * @return array
     **/
    public function getComments()
    {
        return $this->has_many("ProductComment");
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getShipment()
    {
        return $this->has_many_and_belongs_to("Shipment", 'map_product_shipment');
    }

    /**
     * Get details of product
     *
     * @return void
     **/
    public function getDetail()
    {
        return $this->has_one("ProductDescription");
    }

    /**
     * Get Attributes of Product
     *
     * @return array
     **/
    public function getAttributes()
    {
        return $this->has_many_and_belongs_to('attribute', 'map_product_attribute');
    }

    /**
     * get Images
     *
     * @return array
     **/
    public function getImages()
    {
        return $this->has_many('image');
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function getBrand()
    {
        return $this->belongs_to('Brand', 'brand_id');
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function getTax()
    {
        return $this->belongs_to('Tax', 'tax_class_id');
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function getDiscount()
    {
        return $this->belongs_to('ProductDiscount', 'discount_id');
    }
}