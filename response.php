<?php

class ResponseMsg {
  public $speech;
  public $displayText;
  public $source = 'webhook';

  public function __set($name, $value)
  {
      $this->$name = $value;
  }

  public function setText($text)
  {
    $this->__set( 'speech', $text );
    $this->__set( 'displayText', $text );
  }

  public function result()
  {
    return json_decode($this);
  }

}

?>
