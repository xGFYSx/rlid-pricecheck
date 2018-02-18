<?php
echo ini_get('display_errors');
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
			if($_GET['debug'] = 'php'):
				echo '<pre>'.print_r($json,TRUE).'</pre>';
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
					$response->setText($result);
					echo json_encode($result);
					// echo $response->result();
					// die();
				break;

		endswitch;



		switch($text) {
			case 'hi':
				$speech = 'Helloo...';
				break;

			case 'bye':
				$speech = 'Gnight';
				break;

			case 'anything':
				$speech = 'What should i do master?';
				break;

			default:
				$speech = 'Anything you like';
				break;
		}
		// $speech = json_encode($json);
		//
		// $response = new \stdClass();
		// $response->speech = $speech;
		// $response->displayText = $speech;
		// $response->source = 'webhook';
		// echo json_encode($response);
}
else
{
		echo 'Not allowed';
}

?>
