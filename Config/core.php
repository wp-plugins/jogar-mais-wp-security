<?php
/**
 * Jogar Mais WP Security
 *
 * @package Jogar Mais WP Security
 * @author  Victor Freitas
 * @version 1.0.1
 */

namespace JM\Security;

Init::uses( 'remove', 'Controller' );

class Core
{
	/**
	 * Initialize the plugin
	 *
	 * @since 1.0
	 */
	public function __construct()
	{
		$remove = new Remove_Controller();
	}

}