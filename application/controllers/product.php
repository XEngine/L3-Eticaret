<?php

class Product_Controller extends Base_Controller
{

    public $restful = true;

    public function get_index($cat = '', $alias = '')
    {

        //Filtering the Attribute groups for product specific
        if (empty($alias)) ;
        {
            $prod = Product::with(array(
                'getCategory',
                'getCategory.getDescriptions'
            ))->where('alias', '=', $cat)->first();
            $cat = $prod->getCategory[0]->getDescriptions->alias;
            $alias = $prod->alias;
        }
        $category_id = CategoryDescription::with('getCategory')->where('alias', '=', $cat)->only('id');

        $result = Category::with(array(
            "getDescriptions",
            "getTopCat",
            "getTopCat.getDescriptions",
            "getProducts" => function ($query) use ($alias) {
                $query->where('alias', '=', $alias);
            },
            "getProducts.getBrand",
            "getProducts.getImages",
            "getProducts.getDetail",
            "getProducts.getTax",
            "getProducts.getDiscount",
            "getProducts.getAttributes",
            "getProducts.getShipment",
            "getAttributeListing",
            "getAttributeListing.getTopGroup",
        ))->where('id', '=', $category_id)
            ->first();
        Title::put($result->getProducts[0]->getDetail->name);
        /*Get attributes*/
        $topGroups = array();
        foreach ($result->getAttributeListing as $item) {
            array_push($topGroups, $item->getTopGroup->id);
        }
        $topGroups = array_unique($topGroups);

        $groups = array();
        foreach ($result->getAttributeListing as $item) {
            array_push($groups, $item->id);
        }
        $groups = array_unique($groups);

        $belongedGroups = array();
        foreach ($result->getProducts[0]->getAttributes as $item) {
            array_push($belongedGroups, $item->id);
        }

        $attrs = AttributeGroup::with(array(
            'getParentGroup' => function ($query) use ($groups) {
                $query->order_by('sort_order', 'desc');
                $query->where_in('id', $groups);
            },
            'getParentGroup.getAttributes' => function ($query) use ($belongedGroups) {
                $query->where_in('id', $belongedGroups);
            }
        ))->where_in('id', $topGroups)->get();
        return View::make('products.index')->with('product', $result)->with('attrs', $attrs);
    }
}