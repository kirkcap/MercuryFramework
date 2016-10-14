<?php
namespace com\mercuryfw\models;
require_once(__ROOT__."/backend/api/application/helpers/error_helper.php");
use com\mercuryfw\helpers\ErrorCatcher as ErrorCatcher;
use com\mercuryfw\helpers\REST as REST;
use \Exception as Exception;

class configModel{

  private $cfgName;
  private $config;
  private $exception_ocurred = false;
  private $err;
  private $cfgPath;
  private $cfgFile;

  public function __construct($cfgName){
    $this->cfgPath = __ROOT__."/backend/config/";
    $this->cfgName = $cfgName;
    $cfg_files = json_decode(file_get_contents($this->cfgPath . "config_files.json"), true); //Getting config files config data
    if(array_key_exists($cfgName, $cfg_files)){
      $this->cfgFile = $this->cfgPath . $cfg_files[$cfgName];

      try{
        if(file_exists($this->cfgFile)){
          $this->config = json_decode(file_get_contents($this->cfgFile), true);
        }else{
          $this->config = [];
        }

      }catch(Exception $e){
        $this->exception_ocurred = true;
        $this->err = new ErrorCatcher($e);
        REST::getInstance()->response($this->getErrorData()->getFrontEndResponse(), 200);
      }


    }
  }

  public function listAll(){

    $result = $this->config;
    return $result;

  }

  public function findByKey($key){

    $result = [];
    if(array_key_exists($key,$this->config)){
      $result = array($key => $this->config[$key]);
    }

    return $result;

  }

  public function create($key,$data){

    $result = [];
    if(!empty($data)){
      if(array_key_exists($key,$this->config)){
        $result = array('status' => "Error", "msg" => 'No new ' . $this->cfgName ." Config created.", "data" => $data);
      }else{
        foreach($data as $data_key => $data_value){
          $this->config[$data_key] = $data_value;
        }
        try{
          file_put_contents($this->cfgFile, json_encode($this->config));
          $result = array('status' => "Success", "msg" => $this->cfgName . " Config Created Successfully.", "data" => $data);
        }catch(Exception $e){
          $this->exception_ocurred = true;
          $this->err = new ErrorCatcher($e);
        }

      }
    }else
      $result = array('status' => "Error", "msg" => "No data sent", "data" => $data);

    return $result;
  }




  public function change($key, $data){

    if(!empty($data)){
      $result = [];

      if(array_key_exists($key,$this->config)){
        $this->config[$key] = $data[$key];
        try{
          file_put_contents($this->cfgFile, json_encode($this->config));
          $result = array('status' => "Success", "msg" => $this->cfgName . " Config Updated Successfully.", "data" => $data);
        }catch(Exception $e){
          $this->exception_ocurred = true;
          $this->err = new ErrorCatcher($e);
        }

      }else{
        $result = array('status' => "Error", "msg" => 'No ' . $this->cfgName ." updated.", "data" => $data);

      }

    }else
      $result = array('status' => "Error", "msg" => "No data sent", "data" => $data);

    return $result;

  }


  public function delete($key){

    $result = [];
    if(sizeof($key) > 0){

      if(array_key_exists($key,$this->config)){
        unset($this->config[$key]);
        try{
          file_put_contents($this->cfgFile, json_encode($this->config));
          $result = array('status' => "Success", "msg" => $this->cfgName . " Config Deleted Successfully.", "key" => $key);
        }catch(Exception $e){
          $this->exception_ocurred = true;
          $this->err = new ErrorCatcher($e);
        }

      }else{
        $result = array('status' => "Error", "msg" => 'No ' . $this->cfgName ." deleted.", "key" => $key);

      }

    }else {
      $result = array('status' => "Error", "msg" => "No parameter sent !");
    }

    return $result;

  }

  public function exceptionOcurred(){
    return $this->exception_ocurred;
  }

  public function getErrorData(){
    return $this->err;
  }

}

?>
