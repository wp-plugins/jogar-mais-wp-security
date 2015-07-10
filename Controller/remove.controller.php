<?php
/**
 * Jogar Mais WP Security Controller
 *
 * @package Jogar Mais WP Security
 * @subpackage Security
 * @author Victor Freitas
 * @since 1.0
 */

namespace JM\Security;

class Remove_Controller
{
	/**
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __construct()
	{
		add_filter( 'script_loader_src', array( &$this, 'change_version' ) );
		add_filter( 'style_loader_src', array( &$this, 'change_version' ) );
		add_action( 'init', array( &$this, 'remove_header_info' ) );
		add_action( 'init', array( &$this, 'remove_generator' ) );
	}

	/**
	 * Return integer, change filemtime of direct link files
	 * @param string
	 * @since 1.0
	 * @return integer.
	 */
	private function _jm_filemtime_generator( $uri )
	{
        $uri    = parse_url( $uri );
        $handle = @fsockopen( $uri['host'], 80 );

        if ( ! $handle )
            return 0;

        fputs( $handle, "GET $uri[path] HTTP/1.1\r\nHost: $uri[host]\r\n\r\n" );
        $result = 0;

        while ( ! feof( $handle ) ) :

            $line = fgets( $handle, 1024 );

            if ( ! trim( $line ) )
                break;

			$col    = strpos( $line, ':' );
			$header = null;

            if ( $col !== false ):
                $header = trim( substr( $line, 0, $col ) );
                $value  = trim( substr( $line, ( $col + 1 ) ) );
            endif;

            if ( strtolower( $header ) == 'last-modified' )
                $result = strtotime( $value );
                break;

        endwhile;

        fclose( $handle );

        return ( ( $result ) ? Intval( $result ) : date( 'dnY' ) * 2048 );
    }

    public function remove_generator()
    {
		$types = array( 'html', 'xhtml', 'atom', 'rss2', 'rdf', 'comment', 'export', );
		foreach ( $types as $type )
			add_filter( 'get_the_generator_' . $type, '__return_false' );
    }

	public function remove_header_info()
	{
	    remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	    remove_action( 'wp_head', 'start_post_rel_link' );
	    remove_action( 'wp_head', 'wlwmanifest_link' );
	    remove_action( 'wp_head', 'index_rel_link' );
	    remove_action( 'wp_head', 'wp_generator' );
	    remove_action( 'wp_head', 'rsd_link' );
	}

	// change wp version param from any enqueued scripts
	public function change_version( $src )
	{
		global $wp_version;

		$src = esc_url( $src );

		if ( ! $src )
			return $src;

		$file = explode( 'ver=', $src );

		if ( ! isset( $file[1] ) )
			return $src;

		if ( $file[1] === $wp_version )
			return $file[0] . 'ver=' . $this->_change_version( $src, $file );

		return $src;
	}

	//Last modification file
	private function _change_version( $src, $file = array() )
	{
		$path_fix = str_replace( '\\', Init::DS, realpath( esc_html( $_SERVER['DOCUMENT_ROOT'] ) ) );
		$parse    = ( object ) parse_url( $src );

		$version  = $this->_jm_filemtime_generator( $file[0] );
		$mod_date = date( 'dmyHis', $version );

		if ( file_exists( $path_fix . $parse->path ) ) :

			$version  = filemtime( $path_fix . $parse->path );
			$mod_date = date( 'dmyHis', $version );

		endif;

		return $mod_date;
	}

}