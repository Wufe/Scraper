<?php
	
	namespace Scraper\Middleware;
	use Carbon\Carbon;

	class Log{

		public static function log( $message ){
			echo "[ ".Carbon::now()." ] ".$message.PHP_EOL;
		}

	}


?>