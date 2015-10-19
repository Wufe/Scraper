<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;

	class Tree{

		public static function get_node_from_path( $root, $path ){
			if( $path == "" ){
				return $root;
			}else{
				$path = explode( ",", $path );
				foreach( $path as $p ){
					$root = $root[ 'children' ];
					$root = $root[ (int)$p ];
				}
				return $root;
			}
		}

		public static function get_parent_from_path( $root, $path ){
			if( $path == "" ){
				return $root;
			}else{
				$path = explode( ",", $path );
				for( $p = 0; $p < count( $path ) -1; $p++ ){
					$root = $root[ 'children' ];
					$root = $root[ (int)$path[ $p ] ];
				}
				return $root;
			}
		}

		public static function get_siblings( $root, $node ){
			$path = $node[ 'path' ];
			$parent = Tree::get_parent_from_path( $root, $path );
			$siblings = [];
			foreach( $parent[ 'children' ] as $child ){
				if( $child[ 'path' ] != $path ){
					$siblings[] = $child;
				}
			}
			return $siblings;
		}

		public static function get_children( $node ){
			return @!!$node[ 'children' ] ? $node[ 'children' ] : false;
		}

	}

?>