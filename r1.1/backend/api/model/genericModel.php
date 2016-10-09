<?php
namespace com\mercuryfw\models;
require_once(__ROOT__."/backend/api/model/baseModel.php");
require_once(__ROOT__."/backend/api/application/helpers/error_helper.php");
use com\mercuryfw\helpers\ErrorCatcher as ErrorCatcher;
use \Exception as Exception;

class genericModel extends baseModel{

  private $model;
  private $exception_ocurred = false;
  private $err;

  public function listAll($parm, $filter){
    $result = [0];

    try{
      $SelectStmt = $this->prepareDynamicSelectStmt( $this->getTableKey(), $parm, $filter );
      $result = $SelectStmt->execute(genericModel::SelectALL_RECORDS);
    }
    catch(Exception $e){
      $this->exception_ocurred = true;
      $this->err = new ErrorCatcher($e);
    }

    return $result;

  }

  public function findByKey($parm){
    $result = [];

    try{
      $SelectStmt = $this->prepareDynamicSelectStmt( $this->getTableKey(), $parm  );
      $result = $SelectStmt->execute(genericModel::SelectFIRST_ONLY);
    }
    catch(Exception $e){
      $this->exception_ocurred = true;
      $this->err = new ErrorCatcher($e);
    }

    return $result;

  }

  public function find($critfields, $critvalues){

    $result = [];

    try{
      $SelectStmt = $this->prepareDynamicSelectStmt( $critfields, $critvalues  );
      $result = $SelectStmt->execute(genericModel::SelectFIRST_ONLY);
    }
    catch(Exception $e){
      $this->exception_ocurred = true;
      $this->err = new ErrorCatcher($e);
    }

    return $result;

  }


  public function create($parm, $data){

    $result = [];
    if(!empty($data)){

      try{
        $InsertStmt = $this->prepareInsertStmt( $data, $parm );
        $r = $InsertStmt->execute();
        if($r>0){
          $result = array('status' => "Success", "msg" => $this->getEntityName() . " Created Successfully.", "data" => $data);
        }else{
          $result = array('status' => "Error", "msg" => 'No new ' . $this->getEntityName() ." not created.", "data" => $data);
        }
      }
      catch(Exception $e){
        //print_r($e);
        $this->exception_ocurred = true;
        $this->err = new ErrorCatcher($e);
      }

    }else
      $result = array('status' => "Error", "msg" => "No data sent", "data" => $data);

    return $result;
  }




  public function change($parm, $data){

    if(!empty($data)){
      $result = [];

      try{
        $UpdateStmt = $this->prepareUpdateStmt( $data, $this->getTableKey(), $parm );
        $r = $UpdateStmt->execute();
        if($r>0){
          $result = array('status' => "Success", "msg" => $this->getEntityName() ." updated Successfully.", "data" => $data);
        }else{
          $result = array('status' => "Error", "msg" => $this->getEntityName() ." not updated.", "data" => $data);
        }
      }
      catch(Exception $e){
        $this->exception_ocurred = true;
        $this->err = new ErrorCatcher($e);
      }

    }else
      $result = array('status' => "Error", "msg" => "No data sent", "data" => $data);

    return $result;

  }


  public function delete($parm){

    $result = [];
    if(sizeof($parm) > 0){

      try{
        $DeleteStmt = $this->prepareDeleteStmt( $this->getTableKey(), $parm );
        $r = $DeleteStmt->execute();
        if($r>0){
          $result = array('status' => "Success", "msg" => $this->getEntityName() ." deleted Successfully.");
        }else{
          $result = array('status' => "Error", "msg" => 'No ' . $this->getEntityName() ." deleted.");
        }
      }
      catch(Exception $e){
        $this->exception_ocurred = true;
        $this->err = new ErrorCatcher($e);
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
