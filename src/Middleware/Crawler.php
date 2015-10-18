<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;
	use Symfony\Component\DomCrawler\Crawler as DomCrawler;

	class Crawler{

		// Crawl the source into a DOM object
		public static function crawl( $source ){
			$crawler = new DomCrawler( $source );
			Log::log( "Listing node attributes" );
			
			$node_tree 		= Parser::parse( $crawler );

			file_put_contents( "tree", print_r( $node_tree, true ) );

			$augmented_tree	= Parser::augment( $node_tree[ 0 ] );

			file_put_contents( "a-tree", print_r( $augmented_tree, true ) );

			// Test on google.it
			//var_dump( $crawler->filterXPath( "descendant-or-self::html/body/center/form/table/tr/td[position()=2]/span[position()=1]/span/input" )->getNode(0) );
		}

	}


?>