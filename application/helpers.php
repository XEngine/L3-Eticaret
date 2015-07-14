<?php
function sort_arr_of_obj($array, $sortby, $direction = 'asc')
{

    $sortedArr = array();
    $tmp_Array = array();

    foreach ($array as $k => $v) {
        $tmp_Array[] = strtolower($v->$sortby);
    }

    if ($direction == 'asc') {
        asort($tmp_Array);
    } else {
        arsort($tmp_Array);
    }

    foreach ($tmp_Array as $k => $tmp) {
        $sortedArr[] = $array[$k];
    }

    return $sortedArr;

}

function searchForId($col, $id, $array)
{
    foreach ($array as $key => $val) {
        if ($val[$col] === $id) {
            return true;
        }
    }
    return false;
}

function Check_User_Cart()
{
    $Identifier = '';
    if (!Sentry::check()) {
        return false;
    } else {
        $Identifier = Sentry::user()->id;
        if (Cookie::has('Anon_Cart_Extension')) {
            $AnonIdentifier = Cookie::get('Anon_Cart_Extension');
            $dataAnon = Cache::get('user_cart.' . $AnonIdentifier);
            if (Cache::has('user_cart.' . $Identifier)) {
                $dataUser = Cache::get('user_cart.' . $Identifier);
                if ($dataAnon != null && $dataUser != null) {
                    foreach ($dataAnon as $key => $value) {
                        if (!isset($dataUser[$key])) {
                            $dataUser[$key] = $value;
                        }
                    }
                    Cache::forever('user_cart.' . $Identifier, $dataUser);
                    Cache::forget('user_cart.' . $AnonIdentifier);
                }
            } else {
                if ($dataAnon != null) {
                    Cache::forever('user_cart.' . $Identifier, $dataAnon);
                    Cache::forget('user_cart.' . $AnonIdentifier);
                }
            }
        }
    }
}

function arrayToObject($d)
{
    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return (object)array_map(__FUNCTION__, $d);
    } else {
        // Return object
        return $d;
    }
}

function getClosest($search, $arr)
{
    $closest = null;
    foreach ($arr as $item) {
        if ($closest == null || abs($search - $closest) > abs($item - $search)) {
            $closest = $item;
        }
    }
    return $closest;
}

function get_unique_id()
{
    $string = strtoupper(md5(uniqid(rand(), true)));
    $chr_hyphen = chr(45);   // "-"
    $unique_id =
        substr($string, 0, 8) . $chr_hyphen
        . substr($string, 8, 4) . $chr_hyphen
        . substr($string, 12, 4) . $chr_hyphen
        . substr($string, 16, 4) . $chr_hyphen
        . substr($string, 20, 12);

    return $unique_id;
}

/**
 * undocumented function
 *
 * @return array
 **/
function getProductImages($obj)
{
    $ProductCategory = Product::find($obj->id)->getCategory()->first();
    $cat = $ProductCategory->getDescriptions->alias;

    $brd = $obj->getBrand->alias;
    $imgs = $obj->getImages()->order_by('order', 'asc')->get();
    if ($imgs == null)
        return null;
    $cache = 'product.images.' . md5($obj->alias);
    $tmparray = array();
    $x = 0;
    $sizes = array('tiny'   => 40,
                   'tinym'  => 80,
                   'small'  => 200,
                   'medium' => 300,
                   'large'  => 500,
                   'huge'   => 800
    );
    if (!Cache::has($cache)) {
        foreach ($imgs as $k => $v) {
            foreach ($sizes as $key => $val) {
                $img = '/img/products/' . $cat . '/' . $val . '/' . $v->unique_id . '.jpg';
                if ($v->main == 1) {
                    $tmparray['main']['id'] = $v->id;
                    $tmparray['main'][$key] = $img;
                }
                $tmparray['images'][$x][$key] = $img;
            }
            $tmparray['images'][$x]['id'] = $v->id;
            $x++;
        }
        $array = arrayToObject($tmparray);
        Cache::forever($cache, $array);
    }

    return Cache::get($cache);
}

/**
 * Adding Symbols right or left or both to the price
 *
 * @return string
 *
 ***/
function addSymbol($price, $leftsymbol = '', $rightsymbol = '')
{
    if (!empty($leftsymbol)) {
        $price = $leftsymbol . $price;
    }
    if (!empty($rightsymbol)) {
        $price = $price . ' ' . $rightsymbol;
    }
    return $price;
}

/**
 * undocumented function
 *
 * @return array
 **/
function getItemPrice($item, $currency = 'TRY')
{
    if (empty($item->price)) {
        return 0;
    }
    $getCurrency = Currency::where('code', '=', $currency)->first();
    $price = (float)$item->price * $getCurrency->value;
    $tax = $item->getTax;
    $groupDiscount = 0;
    if (!empty($item->getDiscount)) {
        $groupDiscount = $item->getDiscount->discount_value;
    } else {
        $groupDiscount = 1;
    }
    if (!empty($item->discount)) {
        $singleDiscount = $item->discount;
    } else {
        $singleDiscount = 1;
    }
    $discount = !(empty($item->getDiscount)) ? $groupDiscount : $singleDiscount;
    $obj = array();
    $money = $price;
    $awesomeTotal = $price;
    $decimal_point = ',';
    $thousand_point = '.';


    //;This is for making the default price
    $obj['price_raw'] = $price;
    //;This is for making the default price to readable numbers
    $obj['price_normal'] = number_format(round($obj['price_raw'], 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point);
    //;This is for making the default price to readable numbers
    $obj['get_price'] = addSymbol($obj['price_normal'], $getCurrency->symbol_left, $getCurrency->symbol_right);

    $money = !empty($tax) ? $money + (($money * $tax->rate) / 100) : $price;
    $awesomeTotal = !empty($tax) ? $money + (($money * $tax->rate) / 100) : $price;
    //;Taxing..
    $obj['tax_raw'] = !empty($tax) ? round($price + (($price * $tax->rate) / 100), 0) : $price;
    $obj['tax_normal'] = number_format(round($obj['tax_raw'], 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point);
    $obj['get_taxed'] = addSymbol($obj['tax_normal'], $getCurrency->symbol_left, $getCurrency->symbol_right);
    $obj['totaltax'] = addSymbol(number_format(round($obj['tax_raw'] - $obj['price_raw'], 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point), $getCurrency->symbol_left, $getCurrency->symbol_right);


    $money = !empty($discount) ? $money - (($money * $discount) / 100) : $price;
    $awesomeTotal += !empty($discount) ? $money - (($money * $discount) / 100) : $price;
    //;Discounting
    $obj['_discount_raw'] = !empty($discount) ? $price - (($price * $discount) / 100) : $price;
    $obj['_discount_normal'] = number_format(round($obj['_discount_raw'], 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point);
    $obj['_get_discount'] = addSymbol($obj['_discount_normal'], $getCurrency->symbol_left, $getCurrency->symbol_right);
    $obj['discount_raw'] = $money;
    $obj['discount_normal'] = number_format(round($obj['discount_raw'], 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point);
    $obj['get_discount'] = addSymbol($obj['discount_normal'], $getCurrency->symbol_left, $getCurrency->symbol_right);
    $discdiff = !empty($tax) ? $obj['tax_raw'] - $obj['discount_raw'] : $obj['price_raw'] - $obj['_discount_raw'];
    $obj['totaldiscount'] = addSymbol(number_format(round($discdiff, 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point), $getCurrency->symbol_left, $getCurrency->symbol_right);

    $obj['_get'] = $money;
    //;Round the latest value we got. Output : 1.123,23
    $money = addSymbol(number_format(round($money, 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point), $getCurrency->symbol_left, $getCurrency->symbol_right);
    if (!empty($tax)) {
        $money .= ' <small>' . $tax->title . ' DAHİL</small>';
    }
    $obj['get'] = $money;
    return arrayToObject($obj);
}

function getTotal($price, $currency = 'TRY')
{
    $getCurrency = Currency::where('code', '=', $currency)->first();
    $decimal_point = ',';
    $thousand_point = '.';
    return addSymbol(number_format(round($price, 0), (int)$getCurrency->decimal_place, $decimal_point, $thousand_point), $getCurrency->symbol_left, $getCurrency->symbol_right);
}

function itemsTotal($currentcart = null)
{
    if ($currentcart != null) {
        $items = array();
        foreach ($currentcart as $key => $value) {
            array_push($items, $key);
        }
        $prods = Product::where_in('id', $items)->get();
        $price = array("price"    => 0,
                       "discount" => 0,
                       "total"    => 0
        );
        foreach ($prods as $item) {
            $qty = $currentcart[$item->id]['_qty'];
            $_price = getItemPrice($item);
            $price['price'] += floor($_price->price_raw) * $qty;
            $price['total'] += floor($_price->tax_raw) * $qty;
        }
        return arrayToObject(array_map("getTotal", $price));
    } else {
        return false;
    }
}

function sendActivation($mail, $name, $link, $html = true)
{
    Message::send(function ($message) use ($mail, $name, $link, $html) {

        $message->to($mail);
        $message->from('ik@karelgroup.com', 'KarelGroup DTM');
        $message->subject('KarelGroup DTM Üyelik Aktivasyonu');
        $message->body('view: email.activation');
        $message->body->name = $name;
        $message->body->link = $link;
        $message->body->sentdate = date("m/d/Y");
        $message->html($html);
    });
}
