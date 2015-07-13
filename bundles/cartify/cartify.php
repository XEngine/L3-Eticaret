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

namespace Cartify;

/**
 * Cartify Exception.
 */
class CartifyException extends \Exception {}

/**
 * Cartify class.
 */
class Cartify
{
	/**
	 * Return a new Cartify_Cart() object.
	 *
	 * @access   public
	 * @param    string
	 * @return   object
	 */
	public static function cart($cart_name = null)
	{
		// Return the cart object.
		//
		return new Cartify_Cart($cart_name);
	}

	/**
	 * Returns a new Cartify_Cart() object with the wishlist name.
	 *
	 * @access   public
	 * @param    string
	 * @return   object
	 */
	public static function wishlist($cart_name = 'wishlist')
	{
		// Make sure we have a cart name.
		//
		$cart_name = trim($cart_name);
		$cart_name = (is_null($cart_name) or $cart_name == '' ? 'wishlist' : $cart_name);

		// Return the cart object.
		//
		return new Cartify_Cart($cart_name);
	}
}
