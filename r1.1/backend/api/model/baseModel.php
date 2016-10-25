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
require_once(__ROOT__."/backend/api/application/helpers/db_helper.php");
use com\mercuryfw\helpers as helpers;
use com\mercuryfw\helpers\Models as Models;
use com\mercuryfw\helpers\FieldDefault as FieldDefault;
use com\mercuryfw\helpers\FieldDetails as FieldDetails;
use com\mercuryfw\helpers\DBFactory as DBFactory;
use com\mercuryfw\helpers\Token as Token;
use \Exception as Exception;

class baseModel extends helpers\ModelData{
  const SelectFIRST_ONLY = "FIRST";
  const SelectALL_RECORDS = "ALL";

  private $WhereConditionElements;
  private $SelectionElements;
  private $SelectionOrderElements;
  private $InsertionElements;
  private $UpdateElements;

  public function __construct($modelName){

    $models = Models::getInstance();

    $this->setModelData( $modelName, $models->getModelDataArray($modelName) );

  }

  public function _PREPARE_SELECT( ){
    $this->SelectionElements = array();
    return $this;
  }

  public function _ADD_SEL_FIELD($field, $operation=null, $alias=null){
    $se = new SelectionElement($field, $operation, $alias);
    $this->SelectionElements[] = $se;
    return $this;
  }

  public function _ADD_SUM($field, $alias=null){
    $this->_ADD_SEL_FIELD($field, "SUM", $alias);
    return $this;
  }

  public function _ADD_COUNT($field, $alias=null){
    $this->_ADD_SEL_FIELD($field, "COUNT", $alias);
    return $this;
  }

  public function _ADD_MAX($field, $alias=null){
    $this->_ADD_SEL_FIELD($field, "MAX", $alias);
    return $this;
  }

  public function _ADD_MIN($field, $alias=null){
    $this->_ADD_SEL_FIELD($field, "MIN", $alias);
    return $this;
  }


  public function _PREPARE_WHERE($field=null, $operator=null, $value=null){
    $this->WhereConditionElements = array();
    if($field!=null and $operator!=null and $value!=null){
      $this->_Condition($field, $operator, $value);
    }
    return $this;
  }

  public function _CONDITION($field, $operator, $value){

    if($field!=null and $operator!=null and $value!=null){
      $wce = new WhereConditionElement();
      $wce->setField($field);
      $wce->setOperator($operator);
      $wce->setValue($value);
      $this->WhereConditionElements[] = $wce;
    }else{
      throw new Exception("Condition must be filled !");
    }
    return $this;
  }

  public function _AND($field=null, $operator=null, $value=null){
    $wce = new WhereConditionElement();
    $wce->setOperator("AND");
    $this->WhereConditionElements[] = $wce;

    if($field!=null and $operator!=null and $value!=null){
      $this->_Condition($field, $operator, $value);
    }

    return $this;
  }

  public function _OR($field=null, $operator=null, $value=null){
    $wce = new WhereConditionElement();
    $wce->setOperator("OR");
    $this->WhereConditionElements[] = $wce;

    if($field!=null and $operator!=null and $value!=null){
      $this->_Condition($field, $operator, $value);
    }

    return $this;
  }

  public function GROUP_OPEN(){
    $wce = new WhereConditionElement();
    $wce->setOperator("(");
    $this->WhereConditionElements[] = $wce;

    return $this;
  }

  public function GROUP_CLOSE(){
    $wce = new WhereConditionElement();
    $wce->setOperator(")");
    $this->WhereConditionElements[] = $wce;

    return $this;
  }

  public function _PREPARE_ORDER($field=null, $asc_desc=null){
    $SelectionOrderElements = array();
    if($field!=null){
      $this->SelectionOrderElements[] = new SelectionOrderElement($field, $asc_desc);
    }
  }

  public function _ORDER_BY($field, $asc_desc=null){
    $this->SelectionOrderElements[] = new SelectionOrderElement($field, $asc_desc);
  }


  public function prepareDefaultSelectElements($sort=[]){
    $this->_PREPARE_SELECT();
    $this->_PREPARE_ORDER();
    foreach($this->tb_columns->getColumnsMetadata() as $fname => $tbcolobj){

      if($tbcolobj->getFieldDetails()->canShow()){
        $this->_ADD_SEL_FIELD( $tbcolobj->getField(DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB()) );
      }
      if(sizeOf($sort)==0){//Sort sent on request overwrites configuration
        if($tbcolobj->getFieldDetails()->getOrder()!=null){
          $this->_ORDER_BY( $tbcolobj->getField(DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB()), $tbcolobj->getFieldDetails()->getOrder() );
        }
      }
    }
    for($i=0;$i<sizeOf($sort);$i++){
      $order = FieldDetails::SORT_ASC;
      if($sort[$i]->getName()=='_sort'){//Sort ascending
        $sort_fields = explode(",", $sort[$i]->getValue());
        $order = FieldDetails::SORT_ASC;
      }else{//Sort descending
        $sort_fields = explode(",", $sort[$i]->getValue());
        $order = FieldDetails::SORT_DESC;
      }
      for($j=0;$j<sizeOf($sort_fields);$j++){
        $this->_ORDER_BY( $this->tb_columns->getTableColumn($sort_fields[$j])->getField(DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB()), $order );
      }
    }
  }

  public function getSelectStatement(){
    if($this->SelectionElements == null or sizeof($this->SelectionElements)==0){
      $this->prepareDefaultSelectElements();
    }
    return DBFactory::getInstance()->getDB($this->getDbCfgName())->getSelectStatement( $this->getTableName(), $this->SelectionElements, $this->WhereConditionElements, $this->SelectionOrderElements, $this->getMaxRecsSelect() );
  }

  public function getUpdateStatement(){
    return DBFactory::getInstance()->getDB($this->getDbCfgName())->getUpdateStatement( $this->getTableName(), $this->UpdateElements, $this->WhereConditionElements );
  }

  public function getDeleteStatement(){
    return DBFactory::getInstance()->getDB($this->getDbCfgName())->getDeleteStatement( $this->getTableName(), $this->WhereConditionElements );
  }

  public function prepareDynamicSelectStmt( $keys, $values, $filter = [], $sort = []){

    $this->prepareDefaultSelectElements($sort);

    $this->prepareDynamicWhere($keys, $values, $filter);

    return $this->getSelectStatement();

  }

  private function prepareDynamicWhere($keys, $values, $filter=[]){
    $condCounter = 0;
    if(sizeof($keys) > 0){
      $this->_PREPARE_WHERE();
      $values_local = array();

      if($values!=null){
        $keysSet = true;
        $values_local = $this->parse_keys_values($keys, $values);

        $idx = 0;
        $idxkey = 0;

        foreach($values_local as $value){
          if($this->getTableColumn($keys[$idxkey])->getFieldDetails()->getDefault()!=null and
             $this->getTableColumn($keys[$idxkey])->getFieldDetails()->getDefault()->getType()==FieldDefault::type_TOKEN_ID){ //If there is a key field filled with token id, get token automatically
             if($idxkey == 0){
               $this->_CONDITION( $this->getTableColumn($keys[$idxkey++])->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "=", Token::getInstance()->getId() );
               $condCounter += 1;
             }else{
               $this->_AND( $this->getTableColumn($keys[$idxkey++])->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "=", Token::getInstance()->getId() );
               $condCounter += 1;
             }
          }
          if($idxkey == 0){
            $this->_CONDITION( $this->getTableColumn($keys[$idxkey++])->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "=", $values_local[$idx++] );
            $condCounter += 1;
          }else{
            $this->_AND( $this->getTableColumn($keys[$idxkey++])->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "=", $values_local[$idx++] );
            $condCounter += 1;
          }

        }
      }else{
        for($i=0;$i<sizeof($keys);$i++){
          if($this->getTableColumn($keys[$i])->getFieldDetails()->getDefault()!=null and
             $this->getTableColumn($keys[$i])->getFieldDetails()->getDefault()->getType()==FieldDefault::type_TOKEN_ID){ //If there is a key field filled with token id, get token automatically
             if($condCounter == 0){
               $this->_CONDITION( $this->getTableColumn($keys[$i])->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "=", Token::getInstance()->getId() );
               $condCounter += 1;
             }else{
               $this->_AND( $this->getTableColumn($keys[$i])->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "=", Token::getInstance()->getId() );
               $condCounter += 1;
             }
          }
        }
      }
    }

    if($filter!=null){
      if($condCounter==0){
        $this->_PREPARE_WHERE();
      }
      for($i=0;$i<sizeOf($filter);$i++){

        $field_oper = $this->getFieldAndOperation($filter[$i]->getName());
        $field      = $field_oper['field'];
        $operator   = $field_oper['operator'];

        $values = explode(",", $filter[$i]->getValue());
        if(sizeOf($values)==1){
          if(strpos($values[0],'*')!==false){//If value contains willdcard *, LIKE overwrites the operator previously set
            $operator = " LIKE ";
          }else{
            $operator = $field_oper['operator'];
          }
          if($condCounter == 0){
            $this->_CONDITION( $this->getTableColumn($field)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , $operator, $filter[$i]->getValue() );
            $condCounter += 1;
          }else{
            $this->_AND( $this->getTableColumn($field)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , $operator, $filter[$i]->getValue() );
            $condCounter += 1;
          }
        }else{
          for($j=0;$j<sizeOf($values);$j++){
            if(strpos($values[$j],'*')!==false){//If value contains willdcard *, LIKE overwrites the operator previously set
              $operator = " LIKE ";
            }else{
              $operator = $field_oper['operator'];
            }
            if($j==0){
              if($condCounter == 0){
                $this->GROUP_OPEN( )->_CONDITION( $this->getTableColumn($field)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , $operator, $values[$j] );
              }else{
                $this->_AND( )->GROUP_OPEN( )->_CONDITION( $this->getTableColumn($field)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , $operator, $values[$j] );
              }
            }else{
              $this->_OR( $this->getTableColumn($field)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , $operator, $values[$j] );
              $condCounter += 1;
            }
          }
          $this->GROUP_CLOSE( );
        }


      }
    }
  }

  private function getFieldAndOperation($field){
    $result = [];
    $field_oper = explode("|", $field );
    if(sizeOf($field_oper)>1){
      $result['field'] = $field_oper[0];
      switch($field_oper[1]){
        case "lt":
          $operator = " < ";
          break;
        case "le":
          $operator = " <= ";
          break;
        case "gt":
          $operator = " > ";
          break;
        case "ge":
          $operator = " >= ";
          break;
      }
      $result['operator'] = $operator;
    }else{
      $result['field'] = $field;
      $result['operator'] = " = ";
    }
    return $result;
  }

  private function parse_keys_values($keys, $values){
    $values_local = [];

    if(sizeof($keys) > sizeof($values)){
      foreach($values as $value){
        $values_exp = explode( ",", $value );
        foreach($values_exp as $value_exp){
          $values_local[] = $value_exp;
        }
      }

    }else {
      $values_local = $values;
    }
    return $values_local;
  }





  public function prepareInsertStmt( $fields_data, $key_values ){

    $this->InsertionElements = [];
    $value = "";
    $keyidx = 0;
    $keyvals_local = array();

    if($key_values!=null){

      foreach($key_values as $key_value){
        $keyvals_exp = explode( ",", $key_value );
        foreach($keyvals_exp as $keyval_exp){
          $keyvals_local[] = $keyval_exp;
        }
      }

    }
    $keys_data = [];
    for($i=0;$i<sizeof($this->getTableKey());$i++){
      /*If a key field is filled by default with the token Id, always get it from the Authentication Token*/
      if( $this->getTableColumn( $this->getTableKey()[$i] )->getFieldDetails()->getDefault()!= null &&
          $this->getTableColumn( $this->getTableKey()[$i] )->getFieldDetails()->getDefault()->getType() == FieldDefault::type_TOKEN_ID){
        $token = Token::getInstance();
        if($token->getId()!=null){
          $keys_data[$this->getTableKey()[$i]] = $token->getId();
        }
      }elseif(sizeof($keyvals_local) >= $keyidx+1){
        $keys_data[$this->getTableKey()[$i]] = $keyvals_local[$keyidx++];
      }/*elseif(array_key_exists($this->getTableKey()[$i], $fields_data)){
        $keys_data[$this->getTableKey()[$i]] = $fields_data[$this->getTableKey()[$i]];
      }*/

    }

    foreach($this->tb_columns->getColumnsMetadata() as $fname => $tbcolobj){

      if($tbcolobj->getFieldDetails()->isKey() and $tbcolobj->getFieldDetails()->canInsert()){
        if(!array_key_exists($fname, $fields_data)) {
          /*if(sizeof($keyvals_local) >= $keyidx+1){
            $value = $keyvals_local[$keyidx++];
          }else{
            $value = '';
          }*/
          if(array_key_exists($fname, $keys_data)){
            $value = $keys_data[$fname];
          }else{
            $value = '';
          }
        }else{
          $value = $fields_data[$fname];
        }

        $ie = new InsertUpdateElement($tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) ,$value);
        $this->InsertionElements[] = $ie;

      }elseif($tbcolobj->getFieldDetails()->canInsert()){

        if(!array_key_exists($fname, $fields_data)) {
          $value = '';
        }else{
          $value = $fields_data[$fname];
        }
        if($tbcolobj->getFieldDetails()->isPassword()){
          $value = md5($value);
        }
        $ie = new InsertUpdateElement($tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) ,$value);
        $this->InsertionElements[] = $ie;


      }elseif($tbcolobj->getFieldDetails()->getDefault() != null and
              ( $tbcolobj->getFieldDetails()->isKey() or
                $tbcolobj->getFieldDetails()->getDefault()->fillOnInsert() ) ){

        if($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_FUNCTION){ //MySQL function call

          $this->InsertionElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $tbcolobj->getFieldDetails()->getDefault()->getValue() );

        }elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_TOKEN_ID){//JSON WebToken id
          $token = Token::getInstance();
          if($token->getId()!=null){

            $this->InsertionElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $token->getId() );

          }

        }elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_SUBQUERY_MAX){//Subquery max

          $keys = $tbcolobj->getFieldDetails()->getDefault()->getSubQueryCriteriaFields();

          $modelObj = $this;


          //$modelObj->_PREPARE_SELECT()->_ADD_MAX( $modelObj->getTableColumn( $tbcolobj->getFieldDetails()->getDefault()->getSubQueryMaxField() )->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "LastKey" );
          $modelObj->_PREPARE_SELECT()->_ADD_MAX( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) , "LastKey" );

          $idx = 0;
          $firstCondition = true;
          $modelObj->_PREPARE_WHERE();
          /*
          foreach($keys as $key){
            $fvalue = "";
            if(array_key_exists($key, $keys_data)){
              $fvalue = $keys_data[$key];
            }elseif(array_key_exists($key, $fields_data)){
              $fvalue = $fields_data[$key];
            }else{
              throw new Exception("Key field is not present for subquery!");
            }
            if($firstCondition){
              $modelObj->_CONDITION($modelObj->getTableColumn($key)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), "=", $fvalue );
              $firstCondition = false;
            }
            else{
              $modelObj->_AND($modelObj->getTableColumn($key)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), "=", $fvalue );
            }
          }
          */

          foreach($keys_data as $key => $value){
            if($key!=$fname){ //If the key field being processed is not the current field(which is supposed to be a subtable additional key...)
              if($firstCondition){
                $modelObj->_CONDITION($modelObj->getTableColumn($key)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), "=", $value );
                $firstCondition = false;
              }
              else{
                $modelObj->_AND($modelObj->getTableColumn($key)->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), "=", $value );
              }
            }
          }

          $SelectStmt = $modelObj->getSelectStatement();

          $result = $SelectStmt->execute(baseModel::SelectFIRST_ONLY);
          $lastKey = 0;
          if(sizeof($result)>0){
            $lastKey = $result["LastKey"];
          }
          $newKey = $lastKey + 1;
          $this->InsertionElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $newKey );


        }elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_VALUE){//Fixed Value
          $this->InsertionElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $tbcolobj->getFieldDetails()->getDefault()->getValue() );

        }
        elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_VARIABLE){//Variable(TO BE DEFINED)
          $this->InsertionElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $tbcolobj->getFieldDetails()->getDefault()->getValue() );

        }
      }


    }


    return DBFactory::getInstance()->getDB($this->getDbCfgName())->getInsertStatement( $this->getTableName(), $this->InsertionElements );

  }




  public function prepareUpdateStmt( $fields_data, $keys, $values){

    $value = "";
    $this->UpdateElements = [];

    foreach($this->tb_columns->getColumnsMetadata() as $fname => $tbcolobj){

      if($tbcolobj->getFieldDetails()->canUpdate()){
        if(!array_key_exists($fname, $fields_data)) {
          if(sizeof($keyvals_local) >= $keyidx+1){
            $value = $keyvals_local[$keyidx++];
          }else{
            $value = '';
          }
        }else{
          $value = $fields_data[$fname];
        }
        if($tbcolobj->getFieldDetails()->isPassword()){
          $value = md5($value);
        }

        $ue = new InsertUpdateElement($tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ) ,$value);
        $this->UpdateElements[] = $ue;
      }elseif($tbcolobj->getFieldDetails()->getDefault() != null and
                $tbcolobj->getFieldDetails()->getDefault()->fillOnUpdate() ){
        if($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_FUNCTION){ //MySQL function call

          $this->UpdateElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $tbcolobj->getFieldDetails()->getDefault()->getValue() );

        }elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_TOKEN_ID){//JSON WebToken id

          $token = Token::getInstance();
          if($token->getId()!=null){

            $this->UpdateElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $token->getId() );

          }

        }elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_VALUE){//Fixed Value
          $this->UpdateElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $tbcolobj->getFieldDetails()->getDefault()->getValue() );

        }
        elseif($tbcolobj->getFieldDetails()->getDefault()->getType() == FieldDefault::type_VARIABLE){//Variable(TO BE DEFINED)
          $this->UpdateElements[] = new InsertUpdateElement( $tbcolobj->getField( DBFactory::getInstance()->getDB($this->getDbCfgName())->prefixTB() ), $tbcolobj->getFieldDetails()->getDefault()->getValue() );

        }

      }

    }

    $this->prepareDynamicWhere($keys, $values);

    return $this->getUpdateStatement();

  }




  public function prepareDeleteStmt( $keys, $values){

    $this->prepareDynamicWhere($keys, $values);

    return $this->getDeleteStatement();

  }



}








class SelectionElement{
  private $field;
  private $operation;
  private $alias;

  public function __construct($field, $operation=null,$alias=null){
    $this->field = $field;
    $this->operation = $operation;
    $this->alias = $alias;
  }

  public function setField($field){
    $this->field = $field;
  }
  public function getField(){
    return $this->field;
  }

  public function setOperation($operation){
    $this->operation = $operation;
  }
  public function getOperation(){
    return $this->operation;
  }

  public function setAlias($alias){
    $this->alias = $alias;
  }
  public function getAlias(){
    return $this->alias;
  }
}

class InsertUpdateElement{
  private $field;
  private $value;

  public function __construct($field, $value){
    $this->field = $field;
    $this->value = $value;
  }

  public function setField($field){
    $this->field = $field;
  }
  public function getField(){
    return $this->field;
  }


  public function setValue($value){
    $this->value = $value;
  }
  public function getValue(){
    return $this->value;
  }
}



class WhereConditionElement{
  private $field;
  private $operator;
  private $value;

  public function __construct(){
    $this->field = null;
    $this->operator = null;
    $this->value = null;
  }

  public function setField($field){
    $this->field = $field;
  }
  public function getField(){
    return $this->field;
  }

  public function setOperator($operator){
    $this->operator = $operator;
  }
  public function getOperator(){
    return $this->operator;
  }

  public function setValue($value){
    $this->value = $value;
  }
  public function getValue(){
    return $this->value;
  }

  public function isSQLOperator(){
    if($this->field==null and $this->operator!=null and trim($this->operator)!==""){
      return true;
    }else{
      return false;
    }
  }

  public function isFieldCriteria(){
    if($this->field!=null){
      return true;
    }else{
      return false;
    }
  }
}

class SelectionOrderElement{
  private $field;
  private $order;
  const ASC = 'asc';
  const DESC = 'desc';

  public function __construct($field, $order=null){
    $this->field = $field;
    if($order == null){
      $this->order = SelectionOrderElement::ASC;
    }
    else{
      $this->order = $order;
    }
  }

  public function setField($field){
    $this->field = $field;
  }
  public function getField(){
    return $this->field;
  }

  public function setOrder($order){
    $this->order = $order;
  }
  public function getOrder(){
    return $this->order;
  }

}


 ?>
