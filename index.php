<?php 

$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST

if( $method == 'POST' ){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	$text = $json->result->parameters->text;

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
	$speech = json_encode($json);

	$response = new \stdClass();
	$response->speech = $speech;
	$response->displayText = $speech;
	$response->source = 'webhook';
	echo json_encode($response);
}
else{
	echo 'Not allowed';
}

?>