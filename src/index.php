<?php

	include( 'vendor/autoload.php' );

	ini_set( 'max_execution_time', 500 );

	use \Scraper\Controllers\Main as Main;

	Main::exec( "http://www.agiellenews.it/archivio/" );

?>