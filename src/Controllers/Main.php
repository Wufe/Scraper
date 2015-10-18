<?php

	namespace Scraper\Controllers;
	use Scraper\Downloader 	as Downloader;
	use Scraper\Log 		as Log;
	use Scraper\Parser 		as Parser;
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
			
			$node_tree = Parser::parse( $crawler );
			file_put_contents( "tree", print_r( $node_tree, true ) );
		}


	}

?>