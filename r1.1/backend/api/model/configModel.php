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
* @category Models
* @package  com\mercuryfw\models
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

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
