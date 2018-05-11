<?php

class ResponseMsg {
  public $speech;
  public $displayText;
  public $source = 'webhook';

  public function __set($name, $value)
  {
      $this->$name = $value;
  }

  public function setText($speech,$text)
  {
    // $this->__set( 'speech', trim(preg_replace('/\s+/', ' ', $speech)) );
    $this->__set( "\xE2\x9A\xA0 Bot sedang dalam maintenance. Mohon ditunggu ya... \xF4\x80\x82\x92", $speech );
    $this->__set( "\xE2\x9A\xA0 Bot sedang dalam maintenance. Mohon ditunggu ya... \xF4\x80\x82\x92", $text );
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
