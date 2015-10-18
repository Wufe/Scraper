<?php

	namespace Scraper\Controllers;
	use Scraper\Middleware\Downloader;
	use Scraper\Middleware\Log;
	use Scraper\Middleware\Parser;
	use Scraper\Middleware\Crawler;
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

			Crawler::crawl( (string)$source );
		}

		


	}

?>