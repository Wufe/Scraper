<?php

	namespace Scraper\Middleware;

	use Scraper\Middleware\Log;
	use Scraper\Middleware\Crawler;
	use Scraper\Middleware\Tree;
	use Scraper\Middleware\Filter;
	use Scraper\Middleware\Tweakers\Generic as Tweaker;

	class Scanner{

		public static $been_prioritized = [];

		// This function will look for pattern
		public static function scan( $node ){

			// The tweaker has to be applied BEFORE the scan
			Tweaker::apply();

			$priority = Scanner::scan_for_pattern( $node );

			Log::log( "Tweaking results.." );
			//$priority = Tweaker::apply( $priority );

			// Readable test
			$prio = [];
			foreach( $priority as $node ){
				unset( $node[ 'children' ] );
				unset( $node[ 'node' ] );
				$prio[] = $node;
			}
			$priority = $prio;
			// End readable test

			// Test
			Log::log( "Printing results." );
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
		public static function scan_for_pattern( $root, $node = "", $priority = [] ){
			if( @!$node )$node = $root;
			$children = Tree::get_tagged_children( $node );
			if( @!!$children ){
				if( Node::get_class( $node ) == "posts-list-inner" ){
					//$elig = Scanner::are_eligible( $root, $node, $children );
					//Log::log( "Children count is ".count( $children )." ".( $elig ? "and" : "but" )." they are ".( $elig ? "" : "not" )." eligible." );
				}
				if( Scanner::are_eligible( $root, $node, $children ) && !Scanner::has_been_prioritized( $node[ 'path' ] ) ){
					$node[ 'count' ] = count( $children );
					$id = Node::get_id( $node );
					if( $id !== false ){
						$node[ 'id' ] = "#".$id;
					}
					$class = Node::get_class( $node );
					if( $class !== false ){
						$node[ 'class' ] = ".".$class;
					}
					
					$priority[] = $node;
					Scanner::$been_prioritized[] = $node[ 'path' ];
				}
				foreach( $children as $child ){
					$priority = Scanner::scan_for_pattern( $root, $child, $priority );
				}
				
			}
			return $priority;
		}

		// Checks that a group of children are eligible to become the important pattern

		// We need to add another check: check if children are of the same class
		public static function are_eligible( $root, $parent, $nodes ){

			//New methods

			// Here we are registering properties that we are going to filter
			Filter::register_identifier( "\Scraper\Middleware\Node", "get_id", "id" );
			Filter::register_identifier( "\Scraper\Middleware\Node", "get_class", "class" );
			Filter::register_identifier( "\Scraper\Middleware\Node", "get_components", "components" );

			Filter::register_filter( "\Scraper\Middleware\Filters\Depth", "check_depth", "depth", "*" );




			return Filter::filter( $root, $parent, $nodes );





			/*$main_wrap = [];
			// Need to add a blacklist of children node that are not eligible, like <script>
			$parent_tag = Tree::get_parent_from_path( $root, $nodes[ 0 ][ 'path' ] )[ 'tag' ];

			//test thing
			$isObject = Node::get_class( Tree::get_parent_from_path( $root, $nodes[ 0 ][ 'path' ] ) ) == "posts-list-inner";
			$isObject = false;
			
			foreach( $nodes as $node ){
				$found1 = -1;
				$found2 = -1;
				$class = Node::get_class( $node );
				for( $i = 0; $i < count( $main_wrap ); $i++ ){
					
					if( $main_wrap[ $i ][ 'pattern' ] == $node[ 'components' ] ){
						$found1 = $i;
					}
					if( @!!$class && $main_wrap[ $i ][ 'pattern' ] == $class ){
						$found2 = $i;
					}
				}
				if( $found1 > -1 ){
					$main_wrap[ $found1 ][ 'nodes' ][] = $node;
				}else{
					if( $isObject ){
						Log::log( $node[ 'components' ] );
					}
					$main_wrap[] = [ 'pattern' => $node[ 'components' ], 'nodes' => $node ];
				}
				if( $found2 > -1 ){
					$main_wrap[ $found2 ][ 'nodes' ][] = $node;
				}else{
					$main_wrap[] = [ 'pattern' => $class, 'nodes' => $node ];
				}
			}
			$enough = false;

			$prints = [];

			foreach( $main_wrap as $obj ){
				if( count( $obj[ 'nodes' ] ) > 3 ){
					$prints[] = "There are ".count( $obj[ 'nodes' ] )." with the pattern <".$obj[ 'pattern' ].">";
					$enough = true;
				}
			}
			if( $isObject ){
				Log::log( "Eligible children for node [".$parent_tag."]:" );
				foreach( $prints as $print ){
					Log::log( "\t".$print );
				}
			}
			return $enough;*/
		}

		public static function has_been_prioritized( $path ){
			$found = false;
			foreach( Scanner::$been_prioritized as $prioritized ){
				if( $prioritized == $path ){
					$found = true;
				}
			}
			return $found;
		}


		

	}

?>