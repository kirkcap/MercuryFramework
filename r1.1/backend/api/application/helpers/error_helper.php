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
* @category Error dealing
* @package  com\mercuryfw\helpers
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

namespace com\mercuryfw\helpers;
use com\mercuryfw\models\configModel as configModel;
use com\mercuryfw\helpers\Logger as Logger;

class ErrorCatcher{
  private $exception;
  private $exception_data;
  private $cfgData;

  public function __construct($exception){
    $logger = Logger::getInstance();
    $this->exception = $exception;
    $this->exception_data = ['Exception' => ['Exception_Code' => $this->exception->getCode(),
                                             'Originated_On' => $this->exception->getFile(),
                                             'At_Line' => $this->exception->getLine(),
                                             'Message' => $this->exception->getMessage(),
                                             'Complete_Trace' => $this->exception->getTraceAsString()]];
    $logger->log(Logger::LOG_TYPE_Error, $this->exception_data, $exception);

    $cfgModel = new configModel("admin_cfg");
    $this->cfgData = $cfgModel->listAll();

  }

  public function getFrontEndResponse(){
    if(array_key_exists('exception_return', $this->cfgData) && $this->cfgData['exception_return']=='exception_data'){
      $return = $this->exception_data;
    }else{
      if(array_key_exists('exception_friendly_message', $this->cfgData)){
        $return = ["response" => $this->cfgData['exception_friendly_message']];
      }else{
        $return = ["response" => "Oops, something goes wrong!"];
      }
    }

    return $return;
  }
}
 ?>
