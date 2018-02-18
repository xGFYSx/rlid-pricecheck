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
    $this->__set( 'speech', trim(preg_replace('/\s+/', ' ', $text)) );
    $this->__set( 'displayText', $text );
    return $this;
  }

  public function result()
  {
    $result = new stdClass;
    $result->speech = $this->speech;
    $result->displayText = $this->displayText;
    $result->source = $this->source;
    return json_encode($result);
  }

}

?>
