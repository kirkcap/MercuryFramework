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
require_once(__ROOT__."/backend/api/application/helpers/db_helper.php");
use com\mercuryfw\helpers\ErrorCatcher as ErrorCatcher;
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\helpers\DBFactory as DBFactory;
use \Exception as Exception;

class dbMetadataModel{

  private $dbCfgName;
  private $DB;
  private $config;
  private $exception_ocurred = false;
  private $err;

  public function __construct($dbCfgName){

    $this->dbCfgName = $dbCfgName;
    $this->DB = DBFactory::getInstance()->getDB($dbCfgName);

  }

  public function listAll(){

    $result = [];

    try{
      $Stmt = $this->DB->getTablesListStatement();
      $r = $Stmt->execute();
      $tbname = "";
      for($i=0;$i<sizeof($r);$i++){
        foreach($r[$i] as $key => $value){
          if($tbname==""){
            $tbname = $value;
          }else{
            $result[$tbname] = $value;
            $tbname = "";
          }

        }
      }
    }
    catch(Exception $e){
      $this->exception_ocurred = true;
      $this->err = new ErrorCatcher($e);
      //print_r($e);
    }

    return $result;

  }

  public function findByKey($key){

    $result = [0];

    try{
      $Stmt = $this->DB->getTableStructureStatement($key);
      $result = $Stmt->execute();
    }
    catch(Exception $e){
      $this->exception_ocurred = true;
      $this->err = new ErrorCatcher($e);
      //print_r($e);
    }

    return $result;

  }


  private function create($key,$data){

    //No function

  }


  private function change($key, $data){

    //No function

  }


  private function delete($key){

    //No function

  }

  public function exceptionOcurred(){
    return $this->exception_ocurred;
  }

  public function getErrorData(){
    return $this->err;
  }

}

?>
