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
 * The cart main page.
 */
class Cartify_Cart_Controller extends Controller
{
	/**
	 * Flag for whether the controller is RESTful.
	 *
	 * @access   public
	 * @var      boolean
	 */
	public $restful = true;

	/**
	 * Shows the cart contents.
	 *
	 * @access   public
	 * @return   void
	 */
	public function get_index()
	{
		// Get the cart contents.
		//
		$cart_contents = Cartify::cart()->contents();

		// Show the page.
		//
		return View::make('cartify::cart.index')->with('cart_contents', $cart_contents);
	}

	/**
	 * Updates or empties the cart contents.
	 *
	 * @access   public
	 * @return   void
	 */
	public function post_index()
	{
		// If we are updating the items information.
		//
		if (Input::get('update'))
		{
			try
			{
				// Get the items to be updated.
				//
				$items = array();
				foreach(Input::get('items') as $rowid => $qty)
				{
					$items[] = array(
						'rowid' => $rowid,
						'qty'   => $qty
					);
				}

				// Update the cart contents.
				//
				Cartify::cart()->update($items);
			}

			// Is the Item Row ID valid?
			//
			catch (Cartify\CartInvalidItemRowIdException $e)
			{
				// Redirect back to the shopping cart page.
				//
				return Redirect::to('cartify/cart')->with('error', 'Invalid Item Row ID!');
			}

			// Does this item exists on the shopping cart?
			//
			catch (Cartify\CartItemNotFoundException $e)
			{
				// Redirect back to the shopping cart page.
				//
				return Redirect::to('cartify/cart')->with('error', 'Item was not found in your shopping cart!');
			}

			// Is the item quantity valid?
			//
			catch (Cartify\CartInvalidItemQuantityException $e)
			{
				// Redirect back to the shopping cart page.
				//
				return Redirect::to('cartify/cart')->with('error', 'Invalid item quantity!');
			}

			// Redirect back to the shopping cart page.
			//
			return Redirect::to('cartify/cart')->with('success', 'Your shopping cart was updated.');
		}

		// If we are emptying the cart.
		//
		elseif (Input::get('empty'))
		{
			// Let's clear the shopping cart!
			//
			Cartify::cart()->destroy();

			// Redirect back to the shopping cart page.
			//
			return Redirect::to('cartify/cart')->with('success', 'Your shopping cart was cleared!');
		}
	}

	/**
	 * Removes an item from the shopping cart.
	 *
	 * @access   public
	 * @param    string
	 * @return   void
	 */
	public function get_remove($item_id = null)
	{
		try
		{
			// Remove the item from the cart.
			//
			Cartify::cart()->remove($item_id);
		}

		// Is the Item Row ID valid?
		//
		catch (Cartify\CartInvalidItemRowIdException $e)
		{
			// Redirect back to the shopping cart page.
			//
			return Redirect::to('cartify/cart')->with('error', 'Invalid Item Row ID!');
		}

		// Does this item exists on the shopping cart?
		//
		catch (Cartify\CartItemNotFoundException $e)
		{
			// Redirect back to the shopping cart page.
			//
			return Redirect::to('cartify/cart')->with('error', 'Item was not found in your shopping cart!');
		}

		// Other error.
		//
		catch (Cartify\CartException $e)
		{
			// Redirect back to the home page.
			//
			return Redirect::to('cartify/cart')->with('error', 'An unexpected error occurred!');
		}

		// Redirect back to the shopping cart page.
		//
		return Redirect::to('cartify/cart')->with('success', 'The item was removed from the shopping cart.');
	}
}
