<?php
	
	namespace Scraper;

	class Log{

		public static function log( $message ){
			echo "[ ".\Carbon\Carbon::now()." ] ".$message.PHP_EOL;
		}

	}


?>