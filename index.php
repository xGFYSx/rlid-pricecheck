<?php
ini_get('display_errors');
$method = $_SERVER['REQUEST_METHOD'];

include_once dirname(__FILE__) .DIRECTORY_SEPARATOR.  'response.php' ;
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'item.php' ;

$response = new ResponseMsg;


// Process only when method is POST
if( $method == 'POST')
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

		$text = $json->result->parameters->text;
		$intent = $json->result->metadata->intentName;


		switch( $intent ):

				case 'price-intent':
					$item = new Item;
					$result = $item
											->setItem($json->result->parameters->item_color, $json->result->parameters->itemname)
											->setPlatform($json->result->parameters->platform)
											->getPrice();
					echo $response->setText($result);
					die();
				break;

		endswitch;


		//normal response
		switch($text) {
			case '!credit':
				$speech = "Special thanks to \n ........";
				break;

			case '!help':
				$speech = '!help - bantuan \n !price <warna> <harga> <platform> \n !credit - ';
				break;

			default:
				$speech = 'Anything you like';
				break;
		}
		echo $response->setText($speech);
		die();

}
else
{
		echo 'Not allowed';
}

?>
