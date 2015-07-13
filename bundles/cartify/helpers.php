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

/**
 * Returns the supplied number with commas and a decimal point.
 *
 * @param    integer
 * @return   integer
 */
if ( ! function_exists('format_number'))
{
	function format_number($number = null)
	{
		// Check if we have a valid number.
		//
		if (is_null($number))
		{
			return '';
		}

		// Remove anything that isn't a number or decimal point.
		//
		$number = (float) $number;

		// Return the formatted number.
		//
		return number_format($number, 2, '.', ',');
	}
}
