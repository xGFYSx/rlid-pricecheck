<?php
error_reporting( E_ALL );
ini_set('display_errors',1);
$method = $_SERVER['REQUEST_METHOD'];

//include file
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'response.php' ;
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'item.php' ;
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'rank.php' ;

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
														->setPlatform()
														->getPrice();
						echo $response->setText( $result ,$result)->result();
						die();
					break;

					case 'checkrank':
						$rank = new Rank;
						$result = $rank->setQuery($json->result->resolvedQuery)
														->setPlatform($rank->platform)
														->getRank();
						echo $response->setText( $result ,$result)->result();
						die();
					break;
			endswitch;
		}

		//normal response
		switch($text) {
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
