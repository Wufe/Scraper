<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;

	class Filter{
		
		public static $identifiers = [];
		public static $filters = [];

		public static function register_identifier( $class, $method, $name, $prefix = "", $postfix = "" ){
			if( @!self::$identifiers[ $name ] ){
				self::$identifiers[ $name ] = [ "name" => $name, "class" => $class, "method" => $method, "prefix" => $prefix, "postfix" => $postfix ];
			}
		}

		public static function register_filter( $class, $method, $name, $for = "*" ){
			if( @!self::$filters[ $name ] ){
				self::$filters[ $name ] = [ "name" => $name, "class" => $class, "method" => $method, "for" => $for ];
			}
		}

		public static function filter( $root, $parent, $nodes, $verbose = true ){
			$eligible = [];
			$filtered = [];
			$ncount = count( $nodes );
			$invalid = 0;
			foreach( self::$identifiers as $name => $id ){
				foreach( $nodes as $node_key => $node ){
					$identifier = call_user_func( $id[ 'class' ].'::'.$id[ 'method' ], $node );
					if( @!!$identifier ){
						$valid = true;
						foreach( self::$filters as $filter ){
							if( $filter[ 'for' ] == $id[ 'name' ] || $filter[ 'for' ] == '*' ){
								$valid = call_user_func( $filter[ 'class' ]."::".$filter[ 'method' ], $node ) ? $valid : false;

								// Add filtered info into node
							}
						}
						if( @!!$valid ){
							$found = false;
							foreach( $filtered as $filtered_key => $filtered_value ){
								if( $filtered_value[ 'pattern' ] == $identifier ){
									$found = $filtered_key;
								}
							}
							if( $found !== false ){
								$filtered[ $found ][ 'nodes' ][] = $node;
							}else{
								$filtered[] = [ 'pattern' => $identifier, 'id_by' => $id[ 'method' ], "id" => [ 'name' => $id[ 'name' ], 'value' => $id[ 'prefix' ].$identifier.$id[ 'postfix' ] ], 'nodes' => [ $node ] ];
							}
						}else{
							$invalid++;
						}
					}
				}
			}
			
			foreach( $filtered as $filtered_value ){
				$nodes = $filtered_value[ 'nodes' ];
				$nodes_count = count( $nodes );
				$pattern = $filtered_value[ 'pattern' ];
				$id = $filtered_value[ 'id' ];
				if( $nodes_count > 3 ){ // needs to be changed
					if( $verbose ){
						Log::log( "\tThere are ".$nodes_count." nodes with ".strtoupper( $id[ 'name' ] )." pattern <".$id[ 'value' ]."> eligible." );
					}
					$eligible[] = [ 'count' => $nodes_count, 'pattern' => $pattern, 'id_by' => $filtered_value[ 'id_by' ], 'nodes' => $nodes ];
				}
			}

			if( $verbose && $invalid > 0 ){
				//Log::log( "\t".$invalid."/".$ncount." nodes invalidated by filters." ); // Needs to be corrected, because $invalid is incremented by 1 each $filter on each $identifier
			}

			if( count( $eligible ) > 0 ){
				return true;
			}else{
				return false;
			}
			
		}



	}

?>