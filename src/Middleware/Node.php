<?php

	namespace Scraper\Middleware;

	class Node{

		public static function get_id( $node ){
			if( @!$node )$node = func_get_arg( 0 );
			$ret_val = false;
			if( get_class( $node[ 'node' ] ) == "DOMElement" && !!$node[ 'node' ]->getAttribute( 'id' ) ){
				$ret_val = $node[ 'node' ]->getAttribute( 'id' );
			}
			return $ret_val;
		}

		public static function get_class( $node ){
			if( @!$node )$node = func_get_arg( 0 );
			$ret_val = false;
			if( get_class( $node[ 'node' ] ) == "DOMElement" && !!$node[ 'node' ]->getAttribute( 'class' ) ){
				$ret_val = $node[ 'node' ]->getAttribute( 'class' );
			}
			return $ret_val;
		}

		public static function get_components( $node ){
			if( @!$node )$node = func_get_arg( 0 );
			$ret_val = false;
			if( @!!$node[ 'components' ] ){
				$ret_val = $node[ 'components' ];
			}
			return $ret_val;
		}

	}
	
?>