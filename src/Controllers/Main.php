<?php

	namespace Scraper\Controllers;
	use Scraper\Downloader as Downloader;
	use Scraper\Log as Log;
	use Symfony\Component\DomCrawler\Crawler as DomCrawler;

	class Main{

		// Statically download the file and execute the transformation things
		public static function exec( $url ){
			Log::log( "Starting download of the url <".$url.">" );

			// Download the source
			$source = Downloader::get( $url );
			if( $source == false ){
				Log::log( "No source found. Exiting now." );
				exit();
			}

			Main::crawl( (string)$source );
		}

		// Crawl the source into a DOM object
		public static function crawl( $source ){
			$crawler = new DomCrawler( $source );
			Log::log( "Listing node attributes" );
			
			$node_tree = Main::transformDOM( $crawler );
			file_put_contents( "tree", print_r( $node_tree, true ) );
		}

		public static function transformDOM( $node, $deep = 0 ){
			$ret_obj = [];
			$spaces = str_repeat( " ", $deep * 5 );
			if( count( $node ) > 0 ){
				foreach( $node as $domElement ){
					$node_obj = [];
					$nodeName 	= $domElement->nodeName;
					$nodeValue 	= $domElement->nodeValue;
					$tagName 	= isset( $domElement->tagName ) ? $domElement->tagName : "";
					$parentNode = $domElement->parentNode;
					$node_obj = [ 
						'name' 		=> $nodeName,
						'value' 	=> $nodeValue,
						'tag'		=> $tagName,
						'parent'	=> $parentNode
					];

					$log_string = "[".$deep."] ";
					if( $deep > 1 ){
						$tmp_string = "|";
						$tdeep = 0;
						while( $tdeep < $deep ){
							$tmp_string .= str_repeat( " ", 4 )."|";
							$tdeep++;
						}
						$log_string .= $tmp_string;
					}else if( $deep == 1 ){
						$log_string .= "|";
					}
					
					if( $deep != 0 ){
						$log_string .= str_repeat( "-", 4 ).$nodeName;
					}else{
						$log_string .= $nodeName;
					}
					Log::log( $log_string );

					$children 	= Main::transformDOM( $domElement->childNodes, $deep+1 );
					if( !!$children ){
						$node_obj[ 'children' ] = $children;
					}
					$ret_obj[] = $node_obj;
				}
				return $ret_obj;	
			}else{
				return false;
			}
		}
	}

?>