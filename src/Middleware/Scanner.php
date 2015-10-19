<?php

	namespace Scraper\Middleware;

	use Scraper\Middleware\Log;
	use Scraper\Middleware\Crawler;
	use Scraper\Middleware\Tree;

	class Scanner{

		// This function will look for pattern
		public static function scan( $node ){
			$leaves = Scanner::scan_for_leaves( $node );
			$priority = [];
			foreach( $leaves as $leaf ){
				$priority = array_merge( $priority, Scanner::scan_for_pattern( $node, $leaf, $priority ) );
			}
			file_put_contents( "priority", print_r( $priority, true ) );
		}

		// This function will find the leaves of the tree
		public static function scan_for_leaves( $node, $root = false, $leaves = [] ){
			if( !$root )$root = $node;
			if( @!$node[ 'tag' ] && Scanner::has_only_nontagged( Tree::get_parent_from_path( $root, $node[ 'path' ] ) ) ){
				$leaf = Tree::get_parent_from_path( $root, $node[ 'path' ] );
				$leaf[ 'leaf_identified_by' ] = $node[ 'path' ];
				$leaves[] = $leaf;
				return $leaves;
			}else if( @!$node[ 'tag' ] ){
				return $leaves;
			}else{
				if( @!!$node[ 'children' ] ){
					foreach( $node[ 'children' ] as $child ){
						$leaves = Scanner::scan_for_leaves( $child, $root, $leaves );
					}
					return $leaves;
				}else{
					$leaf = Tree::get_node_from_path( $root, $node[ 'path' ] );
					$leaf[ 'leaf_identified_by' ] = $node[ 'path' ];
					$leaves[] = $leaf;
					return $leaves;
				}
			}
		}

		// Checks that a node has only children that do not have the tag attribute
		public static function has_only_nontagged( $node ){
			$ret = true;
			if( @!$node[ 'children' ] ){
				return true;
			}else{
				foreach( $node[ 'children' ] as $child ){
					if( @!!$child[ 'tag' ] ){
						$ret = false;
					}
				}
				return $ret;
			}
		}

		// This function will scan for pattern
		public static function scan_for_pattern( $root, $node, $priority ){
			// Add in priority only if not a child on an existent 
			// and only if the parent is not empty
			if( @!!$node[ 'leaf_identified_by' ] ){
				return Scanner::scan_for_pattern( $root, Tree::get_parent_from_path( $root, $node[ 'path' ] ), $priority );
			}else if( $node[ 'path' ] == "" ){
				return $priority;
			}else{
				$parent = Tree::get_parent_from_path( $root, $node[ 'path' ] );
				$siblings_and_self = Tree::get_children( $parent );
				if( Scanner::are_eligible( $siblings_and_self ) ){
					$priority[] = $parent;
				}
				return Scanner::scan_for_pattern( $root, $parent, $priority );
			}
		}

		// Checks that some children are eligible to become the important pattern
		public static function are_eligible( $nodes ){
			$same = true;
			$image = $nodes[ 0 ][ 'components' ];
			foreach( $nodes as $node ){
				if( $node[ 'components' ] != $image ){
					$same = false;
				}
			}
			return $same;
		}

	}

?>