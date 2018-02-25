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
		$action = $json->result->action;

		//check action, future implementation
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


		//normal response
		switch($text) {
			case '!credit':
				$text = "Special thanks to \n ........";
				break;

			case '!help':
				$text = "!help - bantuan \n !price <warna> <harga> <platform> \n !credit - ";
				break;

			default:
				$text = "Anything you like";
				break;
		}
		echo $response->setText($text,$text)->result();
		die();

}
else
{
		echo 'Not allowed';
}

?>
