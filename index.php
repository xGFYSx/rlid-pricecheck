<?php
error_reporting( E_ALL );
ini_set('display_errors',1);
$method = $_SERVER['REQUEST_METHOD'];

//include file
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'response.php' ;
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'item.php' ;

$response = new ResponseMsg;


// Process only when method is POST
if( $method )
{
		$requestBody = file_get_contents('php://input');
		$json = json_decode($requestBody);

		if( isset($_GET['debug'] )){
			if($_GET['debug'] == 'php'):
				echo '<pre>'.print_r($json->result,TRUE).'</pre>';
			else:
				echo json_encode($json);
			endif;
		}

		//check the text
		if( isset($json->result->resolvedQuery) ){
			$text = $json->result->resolvedQuery;
		}

		//check action
		if( isset($json->result->action) ){
			$action = $json->result->action;
		}

		//check action, future implementation
		if( isset($action) ){

			switch( $action ):

					case 'checkprice':
						$item = new Item;
						$result = $item->setQuery($json->result->resolvedQuery)
														->setPlatform($json->result->parameters->platform)
														->getPrice();

						// echo $response->setText($item->speech,$result)->result();
						echo $response->setText( $result ,$result)->result();
						die();
					break;

			endswitch;
		}


		//normal response
		switch($text) {
			case '!credit':
				$text = "All prices courtesy of https://rl.insider.gg\nSpecial thanks to:\n \xE2\x97\xBE Ruffe - Ananta Rizki F. (Mastermind) \n \xE2\x97\xBE xGFYSx - Dewantara Tirta (Programmer)\n \xE2\x97\xBE FRDS  - Agung Firdaus (Tester and Helper)\n \xE2\x97\xBE Devs at RL Insider (Yggdrasil128 and colleagues)";
				break;

			case '!help':
				$text = "\xE2\x9D\x97 Daftar perintah :\n \xE2\x97\xBE !help - Menampilkan pesan panduan\n \xE2\x97\xBE !price <nama item> <warna (optional)> <platform> - Mengecek harga item\n \xE2\x97\xBE !credit - Menampilkan pesan credit";
				break;

			//no response (biar gak duplikat, atau ngulang kalimat kita)
			default:
				die();
			break;
		}
		echo $response->setText($text,$text)->result();
		die();

}
else{
		echo 'Not allowed';
}

?>
