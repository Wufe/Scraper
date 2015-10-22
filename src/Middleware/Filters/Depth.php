<?php

	namespace Scraper\Middleware\Filters;

	class Depth{

		public static $depth = 2; // The tweak is tweaked in Generic
		
		public static function check_depth( $node ){
			if( @!$node )$node = func_get_arg( 0 );
			$cpts = $node[ 'components' ];
			$depth = 0;
			for( $i = 0; $i <= strlen( $cpts ); $i++ ){
				$char = substr( $cpts, $i, 1 );
				if( $char == "[" ){
					$depth++;
				}else if( $char == "]" ){
					break;
				}
			}
			return $depth >= self::$depth ? true : false; 
		}

	}

?>