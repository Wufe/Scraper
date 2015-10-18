<?php

	include( 'vendor/autoload.php' );

	

	use \Scraper\Controllers\Main as Main;

	Main::exec( "http://www.google.it" );

?>