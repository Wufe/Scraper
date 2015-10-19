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
			Log::log( "Generating tree.." );
			$node_tree 		= Parser::parse( $crawler );
			Log::log( "Calculating components.." );
			$augmented_tree	= Parser::augment( $node_tree[ 0 ] );
			Log::log( "Calculating path.." );
			$marked_tree	= Parser::identify_parent_path( $augmented_tree );

			file_put_contents( "a-tree", print_r( $marked_tree, true ) );
			
			Log::log( "Scanning the tree for a pattern.." );
			Scanner::scan( $marked_tree );

			// Test di funzionamento delle funzioni get_node_from_path e get_parent_from_path
			//Log::log( Tree::get_node_from_path( $marked_tree, "0,0" )[ 'tag' ] );
			//Log::log( Tree::get_parent_from_path( $marked_tree, "0,0" )[ 'tag' ] );
		}

	}


?>