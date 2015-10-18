<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;

	class Parser{

		// Parse a node and its attributes, downward, recursively
		public static function parse( $node, $deep = 0, $xPath = "" ){
			$ret_obj = [];
			$spaces = str_repeat( " ", $deep * 5 );
			$count = count( $node );

			$elements_count = [];

			if( $count > 0 ){
				foreach( $node as $domElement ){

					$node_obj = [];

					$nodeName 	= $domElement->nodeName;
					$nodeValue 	= $domElement->nodeValue;
					$tagName 	= isset( $domElement->tagName ) ? $domElement->tagName : "";
					$parentNode = $domElement->parentNode;

					// Generation of an absolute xPath
					$myPath = "";
					if( $tagName != "" ){
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
					$log_string .= $myPath != "" ? ( " [ ".$myPath." ]" ) : "";
					Log::log( $log_string );

					$node_obj = [ 
						'name' 		=> $nodeName,
						'value' 	=> $nodeValue,
						'tag'		=> $tagName,
						'parent'	=> $parentNode
					];
					
					$children 	= Parser::parse( $domElement->childNodes, $deep+1, $myPath );
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

	}

?>