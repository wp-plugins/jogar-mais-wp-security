<?php
/*
	Plugin Name: WP Security by Jogar Mais
	Plugin URI: http://jogarmais.com.br
	Version: 1.0.0
	Author: Victor Freitas
	Author URI: http://jogarmais.com.br
	License: GPL2
	Text Domain: jm-wp-security
	Description: Remover de forma automática a versão do WordPress do conteúdo HTML de qualquer parte do site.
*/

namespace JM\Security;

class Init
{
	const PLUGIN_SLUG = 'jm-wp-security';

	const DS = DIRECTORY_SEPARATOR;

	public static function uses( $class_name, $location )
	{
		$locations = array(
			'Controller',
		);

		$extension = 'php';

		if ( in_array( $location, $locations ) )
			$extension = strtolower( $location ) . '.php';

		require_once( "{$location}" . self::DS . "{$class_name}.{$extension}" );
	}
}

Init::uses( 'core', 'Config' );

$core = new Core();