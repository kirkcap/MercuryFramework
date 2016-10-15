<?php
namespace com\mercuryfw\helpers;
use \Exception as Exception;

class Models{

  private $ModelsData;

  /**
   * @var Singleton The reference to *Singleton* instance of this class
   */
  private static $instance;

  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   */
  public static function getInstance()
  {
      if (null === static::$instance) {
          static::$instance = new static();
      }

      return static::$instance;
  }

  protected function __construct(){

    $this->ModelsData = json_decode(file_get_contents(__ROOT__.'/backend/config/Models.json'), true); //Getting model data

  }

  /**
   * Private clone method to prevent cloning of the instance of the
   * *Singleton* instance.
   *
   * @return void
   */
  private function __clone(){
  }

  /**
   * Private unserialize method to prevent unserializing of the *Singleton*
   * instance.
   *
   * @return void
   */
  private function __wakeup(){
  }

  public function getModelDataArray($modelname){
    $modelData = null;
    if(array_key_exists($modelname, $this->ModelsData)){
      //$modelData = new ModelData($modelname, $this->ModelsData[$modelname]);
      $modelData = $this->ModelsData[$modelname];
    }else{
      throw new Exception("Model not found:" . $modelname);
    }
    return $modelData;
  }


}

class ModelData{
  protected $modelName;
  protected $dbCfgName;
  protected $entity_id;
  protected $entity_name; // = "Maker";
  protected $tb_name    ; // = "tb_makers";
  protected $tb_key     ; // = array("makcod");
  protected $max_recs_sel;
  protected $login_field;
  protected $pwd_field;
  protected $tb_columns ; /* = array(
                        //'<field>'    => array("key" => true/false, "show" => t/f, "insert" => t/f, "update" => t/f, ["order" => "asc"/"desc"], ["default" => array("type" => "function"/"value"/"variable", "value" => "<value>")])
                          'makcod'     => array("key" => true , "show" => true, "insert" => false, "update" => false),
                          'maknam'     => array("key" => false, "show" => true, "insert" => true , "update" => true, "order" => "asc"),
                          'created_at' => array("key" => false, "show" => true, "insert" => false, "update" => false),
                          'updated_at' => array("key" => false, "show" => true, "insert" => false, "update" => false, "default" => array("type"=> "function", "value" => "current_timestamp"))
                        );*/

  public function __construct($ModelName = null, $ModelData = null){
    if($ModelName != null and $ModelData !=null ){
      $this->setModelData($ModelName, $ModelData);
    }
  }

  public function setModelData($ModelName, $ModelData){
    $this->modelName     = $ModelName;
    if(array_key_exists("dbCfgName",$ModelData)){
      $this->dbCfgName     = $ModelData["dbCfgName"];
    }else{
      $this->dbCfgName     = "default";
    }
    $this->entity_id     = $ModelData["entity_id"];
    $this->entity_name   = $ModelData["entity_name"];
    $this->tb_name       = $ModelData["tb_name"];
    $this->tb_key        = $ModelData["tb_key"];
    if(array_key_exists("max_recs_sel",$ModelData) && $ModelData["max_recs_sel"] > 0){
      $this->max_recs_sel  = $ModelData["max_recs_sel"];
    }else{
      $this->max_recs_sel  = 100;
    }
    if(array_key_exists("login_field",$ModelData)){
      $this->login_field   = $ModelData["login_field"];
      $this->pwd_field     = $ModelData["pwd_field"];
    }
    //$this->tb_columns    = $ModelData["tb_columns"];
    $this->setTableColumns($ModelData["tb_columns"]);

  }

  public function setModelName($name){
    $this->modelName = $name;
  }
  public function getModelName(){
    return $this->modelName;
  }

  public function setDbCfgName($name){
    $this->dbCfgName = $name;
  }
  public function getDbCfgName(){
    return $this->dbCfgName;
  }

  public function setEntityID($ID){
    $this->entity_id = $ID;
  }
  public function getEntityID(){
    return $this->entity_id;
  }

  public function setEntityName($name){
    $this->entity_name = $name;
  }
  public function getEntityName(){
    return $this->entity_name;
  }

  public function setTableName($name){
    $this->tb_name = $name;
  }
  public function getTableName(){
    return $this->tb_name;
  }

  public function setLoginField($field){
    $this->login_field = $field;
  }
  public function getLoginField(){
    return $this->login_field;
  }

  public function setPwdField($field){
    $this->pwd_field = $field;
  }
  public function getPwdField(){
    return $this->pwd_field;
  }

  public function setTableKey($key_array){
    $this->tb_key = $key_array;
  }

  public function getTableKey(){
    return $this->tb_key;
  }

  public function setMaxRecsSelect($data){
    $this->max_recs_sel = $data;
  }

  public function getMaxRecsSelect(){
    return $this->max_recs_sel;
  }

  public function setTableColumns($col_array){
    $this->tb_columns = null;
    if($col_array!=null){
      $this->tb_columns = new TableColumns($this->tb_name, $col_array);
    }else{
      throw new Exception("No table column configured for model ". $this->modelName);
    }
  }

  public function getTableColumns(){
    return $this->tb_columns;
  }

  public function getTableColumn($fname){

    return $this->getTableColumns()->getTableColumn($fname);

  }

}

class TableColumns{
  private $tb_name;
  private $tb_columns;
  private $tb_cols_array;//Temporary

  public function __construct($tb_name, $col_array){
    $this->tb_name = $tb_name;
    if($col_array !=null){
      $this->tb_cols_array = $col_array;
      $this->setTableColumns($col_array);
    }
  }

  public function setTableColumns($col_array){
    $this->tb_columns = array();
    foreach($col_array as $fname => $details){
      $this->tb_columns[$fname] = new TableColumn($this->tb_name, $fname, $details);
    }
  }
  public function getColumnsMetadata(){
    return $this->tb_columns;
  }

  public function getTableColsArray(){
    return $this->tb_cols_array;
  }

  public function getTableColumn($fname){
    if(array_key_exists($fname, $this->tb_columns)){
      return $this->tb_columns[$fname];
    }else{
      throw new Exception('Field "'.$fname. '" not found !');
    }
  }

  public function exists($fname){
    if(array_key_exists($fname, $this->tb_columns)){
      return true;
    }else{
      return false;
    }
  }
}

class FieldData{
  private $prefix;
  private $name;
  private $bind_type;

  public function __construct($name, $bind_type, $prefix=null){

    $this->name = $name;
    $this->prefix = $prefix;
    $this->bind_type = $bind_type;

  }

  public function setName($name){
    $this->name = $name;
  }
  public function getName(){
    return $this->name;
  }

  public function setPrefix($prefix){
    $this->prefix = $prefix;
  }
  public function getPrefix(){
    return $this->prefix;
  }

  public function setBindType($bind_type){
    $this->bind_type = $bind_type;
  }
  public function getBindType(){
    return $this->bind_type;
  }

  public function getFieldName(){
    if($this->prefix == null){
      return $this->name;
    }
    else{
      return $this->prefix . '.' . $this->name;
    }
  }
}

class TableColumn{
  private $tb_name;
  private $fName;
  private $fDetails;

  public function __construct($tb_name, $fname, $details){
    $this->tb_name = $tb_name;
    $this->fName = $fname;
    $this->fDetails = new FieldDetails($details);
  }

  public function setFieldName($fname){
    $this->fName = $name;
  }

  public function getField($prefix_by_tbname=false){
    if($prefix_by_tbname){
      return new FieldData($this->fName, $this->fDetails->getBindType(), $this->tb_name);
    }
    else{
      return new FieldData($this->fName, $this->fDetails->getBindType());
    }

  }
  public function getFieldName($prefix_by_tbname=false){
    if($prefix_by_tbname){
      return $this->tb_name.".".$this->fName;
    }
    return $this->fName;
  }

  public function setFieldDetails($details){
    $this->fDetails = new FieldDetails($details);
  }
  public function getFieldDetails(){
    return $this->fDetails;
  }

}

class FieldDetails{
  const SORT_ASC = 'asc';
  const SORT_DESC = 'desc';
  private $key;
  private $show;
  private $insert;
  private $update;
  private $bind_type;
  private $order;
  private $default;
  private $password;

  public function __construct($fieldDetails){
    if($fieldDetails !=null and is_array($fieldDetails)){
      $this->key = array_key_exists("key",$fieldDetails)?$fieldDetails["key"]:false;
      $this->show = array_key_exists("show",$fieldDetails)?$fieldDetails["show"]:false;
      $this->insert = array_key_exists("insert",$fieldDetails)?$fieldDetails["insert"]:false;
      $this->update = array_key_exists("update",$fieldDetails)?$fieldDetails["update"]:false;
      $this->bind_type = array_key_exists("bind_type",$fieldDetails)?$fieldDetails["bind_type"]:"s";
      $this->order = array_key_exists("order",$fieldDetails)?$fieldDetails["order"]:null;
      $this->default = array_key_exists("default",$fieldDetails)?new FieldDefault($fieldDetails["default"]):null;
      $this->password = array_key_exists("password",$fieldDetails)?$fieldDetails["password"]:false;
    }
  }

  public function setIsKey($key){
    $this->key = $key;
  }
  public function isKey(){
    return $this->key;
  }

  public function setCanShow($show){
    $this->show = $show;
  }
  public function canShow(){
    return $this->show;
  }

  public function setCanInsert($insert){
    $this->insert = $insert;
  }
  public function canInsert(){
    return $this->insert;
  }

  public function setCanUpdate($update){
    $this->update = $update;
  }
  public function canUpdate(){
    return $this->update;
  }

  public function setBindType($type){
    $this->bind_type = $type;
  }
  public function getBindType(){
    return $this->bind_type;
  }

  public function setIsPassword($is){
    $this->password = $is;
  }
  public function isPassword(){
    return $this->password;
  }


  public function setOrder($order){
    $this->order = $order;
  }
  public function getOrder(){
    return $this->order;
  }

  public function setDefault($default){
    $this->default = new FieldDefault($default);
  }
  public function getDefault(){
    return $this->default;
  }

}

class FieldDefault{
  const type_TOKEN_ID = 'token_id';
  const type_FUNCTION = 'function';
  const type_SUBQUERY_MAX = 'subquery_max';
  const type_VARIABLE = 'variable';
  const type_VALUE = 'value';

  private $type;
  private $fill_on_insert;
  private $fill_on_update;
  private $value;
  private $sq_model_name;
  private $sq_field_max;
  private $sq_criteriaFields;

  public function __construct($default=null){
    if($default !=null and is_array($default)){
      $this->type = array_key_exists("type",$default)?$default["type"]:null;
      $this->fill_on_insert = array_key_exists("fill_on_insert",$default)?$default["fill_on_insert"]:null;
      $this->fill_on_update = array_key_exists("fill_on_update",$default)?$default["fill_on_update"]:null;
      $this->value = array_key_exists("value",$default)?$default["value"]:null;
      $this->sq_model_name = array_key_exists("model_name",$default)?$default["model_name"]:null;
      $this->sq_field_max = array_key_exists("field",$default)?$default["field"]:null;
      $this->sq_criteriaFields = array_key_exists("criteriaFields",$default)?$default["criteriaFields"]:null;
    }
  }

  public function setType($type){
    $this->type = $type;
  }
  public function getType(){
    return $this->type;
  }

  public function setFillOnInsert($fill){
    $this->fill_on_insert = $fill;
  }
  public function fillOnInsert(){
    return $this->fill_on_insert;
  }

  public function setFillOnUpdate($fill){
    $this->fill_on_update = $fill;
  }
  public function fillOnUpdate(){
    return $this->fill_on_update;
  }

  public function setValue($value){
    $this->value = $value;
  }
  public function getValue(){
    return $this->value;
  }

  public function setSubQueryModel($model){
    $this->sq_model_name = $model;
  }
  public function getSubQueryModel(){
    return $this->sq_model_name;
  }

  public function setSubQueryMaxField($field){
    $this->sq_field_max = $field;
  }
  public function getSubQueryMaxField(){
    return $this->sq_field_max;
  }

  public function setSubQueryCriteriaFields($fields){
    $this->sq_criteriaFields = $fields;
  }
  public function getSubQueryCriteriaFields(){
    return $this->sq_criteriaFields;
  }

}
 ?>
