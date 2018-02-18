<?php
include_once dirname(__FILE__) .DIRECTORY_SEPARATOR. 'item.php' ;
  $platform = 'pc';
  $item ='white zomba';

  $item = isset($_GET['item']) ? $_GET['item'] : $item;
  $platform = isset($_GET['plat']) ? $_GET['plat'] : $platform;

  echo '<pre>';
  echo $item . "\n";
  echo $platform . "\n";

  $checkitem = new Item;
  $checkitem->item_name = $item;
  $checkitem->setPlatform($platform)->getPrice();

  echo '</pre>';

?>
