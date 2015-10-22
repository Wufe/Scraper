<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;
	use Scraper\Middleware\Filters\Depth;

	// This class is supposed to eliminate the unlikely to be content, in the priority list
	class Tweaker{

		public static $blacklist = [
			'parent' 	=> [],
			'child' 	=> []
		];

		public static $depth = 3;

		public static function apply(){
			Depth::$depth = self::$depth; 	// Depth tweak
		}

		public static function tweak( $priority ){
			$priority = Tweaker::deletion( $priority );
			return $priority;
		}

		public static function deletion( $priority ){
			for( $i = 0; $i < count( $priority ); $i++ ){
				$node = $priority[ $i ];
				$unset = false;
				foreach( self::$blacklist[ 'parent' ] as $parent ){
					if( $node[ 'tag' ] == $parent ){
						unset( $priority[ $i ] );
						$unset = true;
					}
				}
				
				
				foreach( self::$blacklist[ 'child' ] as $child ){
					$old_children = $node[ 'children' ];
					$old_count = count( $old_children );
					for( $a = 0; $a < count( $old_children ); $a++ ){
						$node_child = $old_children[ $a ];
						if( @!!$node_child[ 'tag' ] && $node_child[ 'tag' ] == $child ){
							unset( $old_children[ $a ] );
						}
					}
					if( !$unset ){
						$new_children = $old_children;
						if( count( $new_children ) > 0 ){
							if( count( $new_children ) != $old_count ){
								Log::log( "\tUnset ".( $old_count - count( $new_children ) )." children from [".$node[ 'tag' ]."][".( $i -1 )."]" );
							}
							unset( $priority[ $i ][ 'children' ] );
							$priority[ $i ][ 'children' ] = $new_children;
						}else{
							Log::log( "\tCompletely unset [".$node[ 'tag' ]."][".( $i -1 )."]" );
							unset( $priority[ $i ] );
						}	
					}
				}
				
			}
			return $priority;
		}


	}

?>