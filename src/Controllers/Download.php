<?php

	namespace Scraper\Controllers;

	class Download{

		public function __construct(){
			echo (string)\Scraper\Downloader::get( "http://localhost:8000" );
		}

	}

?>