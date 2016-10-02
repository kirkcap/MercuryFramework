<?php
namespace com\mercuryfw\models;
require_once(__ROOT__."/backend/api/application/helpers/error_helper.php");
require_once(__ROOT__."/backend/api/application/helpers/db_helper.php");
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
