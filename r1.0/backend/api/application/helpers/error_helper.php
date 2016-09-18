<?php

class ErrorCatcher{
  private $message;
  private $exception;
  private $source;

  public function __construct($exception){
    $this->exception = $exception;
  }

  public function getFrontEndResponse(){
    $return = 'Exception code '
              .$this->exception->getCode()
              .' originated on '
              .$this->exception->getFile()
              .' at line '
              .$this->exception->getLine()
              .' Message: '
              .$this->exception->getMessage()
              .' Complete trace:'
              .$this->exception->getTraceAsString();
    return $return;
  }
}
 ?>
