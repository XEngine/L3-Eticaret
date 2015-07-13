<?php
/**
 * --------------------------------------------------------------------------
 * Cartify
 * --------------------------------------------------------------------------
 *
 * Cartify, a shopping cart bundle for use with the Laravel Framework.
 *
 * @package  Cartify
 * @version  2.1.1
 * @author   Bruno Gaspar <brunofgaspar1@gmail.com>
 * @link     https://github.com/bruno-g/cartify
 */

/*
 * --------------------------------------------------------------------------
 * Return the configuration array.
 * --------------------------------------------------------------------------
 */
return array(
	/*
	|--------------------------------------------------------------------------
	| Cart name
	|--------------------------------------------------------------------------
	|
	| Specify the main cart name.
	|
	*/
	'cart_name' => 'cartify_cart',


	/*
	|--------------------------------------------------------------------------
	| Storage Type
	|--------------------------------------------------------------------------
	|
	| You can specify how do you want to storage your user's cart information.
	|
	| Available types of storage:
	|	- session
	|	- cookie
	|	- database
	|
	*/
	'storage_type' => 'session'
);
