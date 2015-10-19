<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;

	class Parser{

		// Parse a node and its attributes, downward, recursively
		public static function parse( $node, $deep = 0, $xPath = "", $verbose = false ){
			$ret_obj = [];
			$spaces = str_repeat( " ", $deep * 5 );
			$count = count( $node );

			$elements_count = [];

			if( $count > 0 ){
				foreach( $node as $domElement ){

					$node_obj = [];

					$nodeName 	= $domElement->nodeName;
					$nodeValue 	= $domElement->nodeValue;
					$tagName 	= isset( $domElement->tagName ) ? $domElement->tagName : false;
					$parentNode = $domElement->parentNode;

					$was_verbose = $verbose;

					if( Parser::get_id( $domElement ) == "g-items-atf" ){
						//$verbose = true;
					}

					// Generation of an absolute xPath
					$myPath = "";
					if( isset( $tagName ) ){
						if( !isset( $elements_count[ $tagName ] ) ){
							$elements_count[ $tagName ] = 1;
						}else{
							$elements_count[ $tagName ]++;
						}

						$myPath = $xPath.( $xPath == "" ? "descendant-or-self::" : "/" ).$tagName.( Parser::tags_count( $node, $tagName ) > 1 ? ( "[position()=".$elements_count[ $tagName ]."]" ) : "" );
					}
					
					// Logging node by name with a tree structure
					$log_string = "[".$deep."]".( strlen( $deep."" ) < 2 ? " " : "" );
					if( $deep == 0 ){
						$log_string .= $nodeName;
					}else{
						if( $deep > 1 ){
							$tmp_string = "|";
							$tdeep = 0;
							while( $tdeep < $deep-1 ){
								$tmp_string .= str_repeat( " ", 4 )."|";
								$tdeep++;
							}
							$log_string .= $tmp_string;
						}else if( $deep == 1 ){
							$log_string .= "|";
						}
						$log_string .= str_repeat( "-", 4 ).$nodeName;
					}
					if( get_class( $domElement ) == "DOMElement" && @!!$domElement->getAttribute( 'id' ) ){
						$log_string .= "[#".$domElement->getAttribute( 'id' )."]";	
					}
					//$log_string .= "[".get_class( $domElement )."]";
					if( $verbose )Log::log( $log_string );

					$node_obj = [ 
						'name' 		=> $nodeName,
						'value' 	=> "",//$nodeValue,
						'parent'	=> "",//$parentNode,
						'node' 		=> $domElement
					];
					if( $tagName !== false ){
						$node_obj[ 'tag' ] = $tagName;
					}
					
					$children 	= Parser::parse( $domElement->childNodes, $deep+1, $myPath, $verbose );
					if( !!$children ){
						$node_obj[ 'children' ] = $children;
					}
					if( $myPath != "" ){
						$node_obj[ 'xPath' ] = $myPath;
					}
					$ret_obj[] = $node_obj;
				}
				return $ret_obj;	
			}else{
				return false;
			}
		}

		// here we make the augmented parse tree that will eventually add relative xPath and sum of the content
		public static function augment( $node ){
			$node[ 'components' ] 	= Parser::get_components( $node );
			// rxPath stands for "relative XPath"
			$node[ 'rxPath' ] 		= Parser::get_relative_xpath( $node );
			$new_children = [];
			if( @!!$node[ 'children' ] ){
				foreach( $node[ 'children' ] as $child ){
					$child 	= Parser::augment( $child );
					$new_children[] = $child;
				}
				unset( $node[ 'children' ] );
				$node[ 'children' ] = $new_children;
			}
			return $node;
			
		}

		// here we get the components of the node, otherwise known as the string the has the
		// sum of the components which root is the node
		public static function get_components( $node ){;
			if( @!$node[ 'tag' ] ){
				$ret_val = "";//"[".$node[ 'name' ]."]";
			}else if( @!$node[ 'children' ] ){
				$ret_val = $node[ 'name' ];
			}else{
				$nodes = [];
				foreach( $node[ 'children' ] as $child ){
					$components = Parser::get_components( $child );
					if( @!!$components )$nodes[] = $components;
				}
				$result = $node[ 'tag' ].( @!!$nodes ? ( "[".implode( "+", $nodes )."]" ) : "" );
				$ret_val = $result;
			}
			return $ret_val;
		}

		// function to help get the relative xpath of a node
		public static function get_relative_xpath( $node ){
			if( @!$node[ 'tag' ] ){
				return "";
			}else if( @!$node[ 'children' ] ){
				return $node[ 'tag' ];
			}else{
				$nodes = [];
				foreach( $node[ 'children' ] as $child ){
					$child_xpath = Parser::get_relative_xpath( $child );
					if( $child_xpath != "" ){
						$nodes[] = $child_xpath;
					}
					if( count( $nodes ) > 0 ){
						return $node[ 'tag' ]."[".implode( "+", $nodes )."]";
					}else{
						return $node[ 'tag' ];
					}
				}
			}
		}

		// get the amount of tags of the same kind, on the same level
		public static function tags_count( $node, $tag ){
			if( count( $node ) > 0 ){
				$count = 0;
				foreach( $node as $element ){
					if( isset( $element->tagName ) && $element->tagName == $tag ){
						$count++;
					}
				}
				return $count++;
			}else{
				return 0;
			}
		}

		public static function identify_parent_path( $node, $path = "" ){
			$node[ 'path' ] = $path;
			if( @!$node[ 'tag' ] ){
				return $node;
			}else{
				if( @!!$node[ 'children' ] ){
					$new_children = [];
					$count = 0;
					foreach( $node[ 'children' ] as $child ){
						$new_path = $path.( $path == "" ? "" : "," ).$count;
						$new_children[] = Parser::identify_parent_path( $child, $new_path );
						$count++;
					}
					unset( $node[ 'children' ] );
					$node[ 'children' ] = $new_children;
					return $node;
				}else{
					return $node;
				}
				
			}
		}

		public static function get_id( $node ){
			$ret_val = false;
			if( get_class( $node ) == "DOMElement" && !!$node->getAttribute( 'id' ) ){
				$ret_val = $node->getAttribute( 'id' );
			}
			return $ret_val;
		}

	}

?>