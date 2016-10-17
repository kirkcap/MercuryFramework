<?php
/*
Copyright 2016 Wilson Rodrigo dos Santos - wilson.santos@gmail.com

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
/**
* PHP version 5
*
* @category Error catching
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

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
