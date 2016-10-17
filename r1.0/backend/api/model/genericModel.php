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
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

require_once(__ROOT__."/backend/api/model/baseModel.php");
require_once(__ROOT__."/backend/api/application/helpers/error_helper.php");

class genericModel extends baseModel{

  private $model;
  private $exception_ocurred = false;
  private $err;

  public function listAll($parm){
    $result = [0];

    try{
      $SelectStmt = $this->prepareDynamicSelectStmt( $this->getTableKey(), $parm  );
      $result = $SelectStmt->execute(genericModel::SelectALL_RECORDS);
    }
    catch(Exception $e){
      $this->exception_ocurred = true;
      $this->err = new ErrorCatcher($e);
      //print_r($e);
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
      //print_r($e);
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
      //print_r($e);
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
        //print_r($e);
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
        //print_r($e);
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
