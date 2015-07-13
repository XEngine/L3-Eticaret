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


#############################################################
############ This file is for the examples only! ############
#############################################################


/**
 * Libraries we can use.
 */
use Cartify\Models\Products;


/**
 * The wishlist page.
 */
class Cartify_Wishlist_Controller extends Controller
{
	/**
	 * Flag for whether the controller is RESTful.
	 *
	 * @access   public
	 * @var      boolean
	 */
	public $restful = true;

	/**
	 * Show a list of the products on the wishlist.
	 *
	 * @access   public
	 * @return   void
	 */
	public function get_index()
	{
		// Show the page.
		//
		return View::make('cartify::wishlist');
	}

	/**
	 * Empties the wishlist contents.
	 *
	 * @access   public
	 * @return   void
	 */
	public function post_index()
	{
		// If we are emptying the wishlist.
		//
		if (Input::get('empty'))
		{
			// Let's make the cart empty!
			//
			Cartify::wishlist()->destroy();

			// Redirect back to the cart home.
			//
			return Redirect::to('cartify/wishlist')->with('warning', 'Your wishlist was cleared!');
		}

		// Redirect back to the wishlist page.
		//
		return Redirect::to('cartify/wishlist');
	}

	/**
	 * Removes an item from the wishlist.
	 *
	 * @access   public
	 * @param    string
	 * @return   void
	 */
	public function get_remove($rowid = null)
	{
		try
		{
			// Remove the item from the wishlist.
			//
			Cartify::wishlist()->remove($rowid);
		}

		// Is the Item Row ID valid?
		//
		catch (Cartify\CartInvalidItemRowIdException $e)
		{
			// Redirect back to the wishlist page.
			//
			return Redirect::to('cartify/wishlist')->with('error', 'Invalid Item Row ID!');
		}

		// Does this item exists on the wishlist?
		//
		catch (Cartify\CartItemNotFoundException $e)
		{
			// Redirect back to the wishlist page.
			//
			return Redirect::to('cartify/wishlist')->with('error', 'Item was not found in your wishlist!');
		}

		// Redirect back to the wishlist page.
		//
		return Redirect::to('cartify/wishlist')->with('success', 'The item was removed from the wishlist.');
	}

	/**
	 * Adds an item from the wishlist to the shopping cart.
	 *
	 * @access   public
	 * @param    string
	 * @return   Redirect
	 */
	public function get_add_to_cart($rowid = null)
	{
		try
		{
			// Get the item information from the wishlist cart.
			//
			$item = Cartify::wishlist()->item($rowid);

			// Make sure the quantity is 1, since we can add the item to the wishlist multiple times!
			//
			$item['qty'] = 1;

			// Add the item to the shopping cart.
			//
			Cartify::cart()->insert($item);
		}
		catch (Cartify\CartInvalidItemIdException $e)
		{
			// Redirect back to the wishlist page.
			//
			return Redirect::to('cartify/wishlist')->with('error', 'Invalid Item Row ID!');
		}
		catch (Cartify\CartItemNotFoundException $e)
		{
			// Redirect back to the wishlist page.
			//
			return Redirect::to('cartify/wishlist')->with('error', 'Item was not found in your wishlist!');
		}

		// Redirect to the shopping cart page.
		//
		return Redirect::to('cartify/cart')->with('success', 'The item was added to your shopping cart!');
	}
}
