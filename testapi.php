<?php
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'item.php' ;
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'response.php' ;
  $platform = 'pc';
  $item ='saffron zomba';

  $item = isset($_GET['item']) ? $_GET['item'] : $item;
  $platform = isset($_GET['plat']) ? $_GET['plat'] : $platform;

  $checkitem = new Item;
  $checkitem->item_name = $item;
  $result = $checkitem->setPlatform($platform)->getPrice();
  // print_r($result);
  // print_r($checkitem->response);

  $response = new ResponseMsg;
  $result = $response->setText($result)
          ->result();

  print_r($result);

?>
