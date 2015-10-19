<?php

	namespace Scraper\Middleware;
	use Scraper\Middleware\Log;
	use Scraper\Middleware\Parser;
	use Scraper\Middleware\Scanner;
	use Symfony\Component\DomCrawler\Crawler as DomCrawler;

	class Crawler{

		// Crawl the source into a DOM object
		public static function crawl( $source ){
			$crawler = new DomCrawler( $source );
			$node_tree 		= Parser::parse( $crawler );
			$augmented_tree	= Parser::augment( $node_tree[ 0 ] );
			$marked_tree	= Parser::identify_parent_path( $augmented_tree );

			Scanner::scan( $marked_tree );

			// Test di funzionamento delle funzioni get_node_from_path e get_parent_from_path
			//Log::log( Tree::get_node_from_path( $marked_tree, "0,0" )[ 'tag' ] );
			//Log::log( Tree::get_parent_from_path( $marked_tree, "0,0" )[ 'tag' ] );
		}

	}


?>