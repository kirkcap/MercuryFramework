<?php
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
