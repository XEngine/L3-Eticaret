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
 * Register the namespaces.
 * --------------------------------------------------------------------------
 */
Autoloader::namespaces(array(
	'Cartify' => __DIR__ . DS
));

/*
 * --------------------------------------------------------------------------
 * Set the global alias.
 * --------------------------------------------------------------------------
 */
Autoloader::alias('Cartify\\Cartify', 'Cartify');
Autoloader::alias('Cartify\\CartifyException', 'CartifyException');

/*
 * --------------------------------------------------------------------------
 * Include our helpers file.
 * --------------------------------------------------------------------------
 */
require_once __DIR__ . DS . 'helpers.php';
