<?php

	namespace Scraper\Middleware\Tweakers;
	use Scraper\Middleware\Tweaker;

	class Generic extends Tweaker{

		public static $depth = 4;

		public static $blacklist = [
			'parent' 	=> [ 'html', 'head', 'body', 'script', 'style', 'meta' ],
			'child' 	=> [ 'head', 'script', 'meta', 'body' ]
		];

		public static function apply(){
			parent::$blacklist = self::$blacklist;
			parent::$depth = self::$depth;
			parent::apply();
		}

		public static function tweak( $priority ){
			return parent::tweak( $priority );
		}

		

	}

?>