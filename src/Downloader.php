<?php

	namespace Scraper;
	use \GuzzleHttp\Client as Guzzle;

	class Downloader{
		
		public static function get( $url ){
			$client = new Guzzle();
			$res = null;
			$tries = 0;
			do{
				try{
					$res = $client->request( 'GET', $url, [ 'timeout' => 10 ]);	
				}catch( \GuzzleHttp\Exception\ConnectException $e ){
					echo "ConnectException".PHP_EOL;
				}
				
				$tries++;
			}while( ( !$res || $res->getStatusCode() == 200 ) && $tries < 10 );
			return $res != null ? $res->getBody() : null;
		}

	}

?>