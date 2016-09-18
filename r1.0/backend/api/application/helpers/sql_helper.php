<?php
/*
class sqlHelper{

  /**
   * @var Singleton The reference to *Singleton* instance of this class
   /
  private static $instance;

  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   /
  public static function getInstance()
  {
      if (null === static::$instance) {
          static::$instance = new static();
      }

      return static::$instance;
  }

  protected function __construct(){

  }



  public static function prepareSelectStmtParts( $tb_name, $tb_columns, $keys, $parms){
    $columns     = "";
    $bind_types  = "";
    $parameters  = [];
    $order       = "";
    $SelectStmt  = new SelectStatement();


    foreach($tb_columns->getColumnsMetadata() as $fname => $tbcolobj){

      if($tbcolobj->getFieldDetails()->canShow()){
        $columns .= $tbcolobj->getFieldName(DBFactory::getInstance()->getDB()->prefixTB()) .',';
      }
    }
    /*
    foreach($tb_columns as $fname => $details){ // Check the customer received. If blank insert blank into the array.
      if(strtolower(substr($fname,0,6)) == "count(" or
         strtolower(substr($fname,0,4)) == "sum(" or
         strtolower(substr($fname,0,4)) == "max(" or
         strtolower(substr($fname,0,4)) == "min("){
        $columns = $columns.$fname.',';
      }elseif(!is_array($details) and
              (strtolower(substr($details,0,6)) == "count(" or
               strtolower(substr($details,0,4)) == "sum(" or
               strtolower(substr($details,0,4)) == "max(" or
               strtolower(substr($details,0,4)) == "min(")){
        $columns = $columns.$details.',';
      }else{
        if($details["show"]){
          $columns = $columns.$fname.',';
        }
        if(array_key_exists("order",$details)){
          $order = $order.$fname.' '.$details["order"].',';
        }
      }

    }
    * /

    $WhereCond   = new WHERECondition($tb_columns, $keys, $parms);

    $SelectStmt->setColumns(trim($columns,','));
    $SelectStmt->setTbName($tb_name);
    $SelectStmt->setWHERECond($WhereCond);
    $SelectStmt->setOrder(trim($order,','));
    $SelectStmt->buildStmt(); //Builds Select Statement

    return $SelectStmt;
  }


  public static function prepareSubqueryStmtParts( $tb_name, $sqy_columns, $tb_columns, $keys, $parms){
    $columns     = "";
    $bind_types  = "";
    $parameters  = [];
    $order       = "";
    $SelectStmt  = new SelectStatement();

    foreach($sqy_columns as $fname => $details){
      if(strtolower(substr($fname,0,6)) == "count(" or
         strtolower(substr($fname,0,4)) == "sum(" or
         strtolower(substr($fname,0,4)) == "max(" or
         strtolower(substr($fname,0,4)) == "min("){
        $columns = $columns.$fname.',';
      }elseif(!is_array($details) and
              (strtolower(substr($details,0,6)) == "count(" or
               strtolower(substr($details,0,4)) == "sum(" or
               strtolower(substr($details,0,4)) == "max(" or
               strtolower(substr($details,0,4)) == "min(")){
        $columns = $columns.$details.',';
      }else{
        if($details["show"]){
          $columns = $columns.$fname.',';
        }
        if(array_key_exists("order",$details)){
          $order = $order.$fname.' '.$details["order"].',';
        }
      }

    }

    $WhereCond   = new WHERECondition($tb_columns, $keys, $parms);

    $SelectStmt->setColumns(trim($columns,','));
    $SelectStmt->setTbName($tb_name);
    $SelectStmt->setWHERECond($WhereCond);
    $SelectStmt->setOrder(trim($order,','));
    $SelectStmt->buildStmt(); //Builds Select Statement

    return $SelectStmt;
  }



  public static function prepareInsertStmtParts( $tb_name , $tb_columns, $fields_data, $parms){
    $columns = "";
    $values = "";
    $value = "";
    $b = 0;
    $bind_types  = "";
    $parameters  = [];
    $keyidx = 0;
    $parms_local = array();

    $InsertStmt  = new InsertStatement();
    if($parms!=null){

      foreach($parms as $parm){
        $parms_exp = explode( ",", $parm );
        foreach($parms_exp as $parm_exp){
          $parms_local[] = $parm_exp;
        }
      }

    }

    foreach($tb_columns as $fname => $details){
      if($details["key"] and $details["insert"]){
        if(!array_key_exists($fname, $fields_data)) {
          if(sizeof($parms_local) >= $keyidx+1){
            $value = $parms_local[$keyidx++];
          }else{
            $value = '';
          }
        }else{
          $value = $fields_data[$fname];
        }
        $columns = $columns.$fname.',';

        $values = $values."?,";
        $bind_types .= $details["bind_type"];
        $parameters[$b++] = $value;

      }elseif($details["insert"]){
        if(!array_key_exists($fname, $fields_data)) {
          $value = '';
        }else{
          $value = $fields_data[$fname];
        }
        $columns = $columns.$fname.',';
        if(array_key_exists('password', $details) && $details["password"]){
          $value = md5($value);
        }

        $values = $values."?,";
        $bind_types .= $details["bind_type"];
        $parameters[$b++] = $value;

      }elseif(array_key_exists("default",$details) and array_key_exists("fill_on_insert", $details["default"])){
        if($details["default"]["type"] == "function"){//MySQL function call
          $columns = $columns.$fname.',';

          $values = $values."?,";
          $bind_types .= $details["bind_type"];
          $parameters[$b++] = $details["default"]["value"];

        }elseif($details["default"]["type"] == "token_id"){//JSON WebToken id
          $token = Token::getInstance();
          if($token->getId()!=null){
            $columns = $columns.$fname.',';

            $values = $values."?,";
            $bind_types .= $details["bind_type"];
            $parameters[$b++] = $token->getId();
          }

        }elseif($details["default"]["type"] == "subquery_max"){//Subquery max

          $keys = $details["default"]["criteriaFields"]; //explode(",", $details["default"]["criteriaFields"]);
          $fvalues = array();
          $idx = 0;
          foreach($keys as $key){
            if(array_key_exists($key, $fields_data)){
              $fvalues[$idx++] = $fields_data[$key];
            }else {
              $fvalues[$idx] = $parms_local[$idx++];
            }
          }

          $SelectStmt = SQLHelper::prepareSubqueryStmtParts( $dbi, $details["default"]["tb_name"], array("max(".$details["default"]["field"].")"), $tb_columns, $keys, $fvalues);
          $result = $SelectStmt->execute(SelectStatement::FIRST_ONLY);
          if(sizeof($result)>0){
            $newSeq = $result["max(".$details["default"]["field"].")"] + 1;
            $columns = $columns.$fname.',';

            $values = $values."?,";
            $bind_types .= $details["bind_type"];
            $parameters[$b++] = $newSeq;
          }


        }elseif($details["default"]["type"] == "value"){//Fixed Value
          $columns = $columns.$fname.',';

          $values = $values."?,";
          $bind_types .= $details["bind_type"];
          $parameters[$b++] = $details["default"]["value"];
        }
        elseif($details["default"]["type"] == "variable"){//Fixed Value
          $columns = $columns.$fname.',';

          $values = $values."?,";
          $bind_types .= $details["bind_type"];
          $parameters[$b++] = $details["default"]["value"];
        }
      }
    }

    $InsertStmt->setTbName( $tb_name );
    $InsertStmt->setColumns( trim($columns,',') );
    $InsertStmt->setValues( trim($values,',') );
    $InsertStmt->setBindTypes( $bind_types );
    $InsertStmt->setParameters( $parameters );

    $InsertStmt->buildStmt(); //Builds Select Statement

    return $InsertStmt;

  }


  public static function prepareUpdateStmtParts( $tb_name , $tb_columns, $fields_data, $keys, $parms){
    $columns = "";
    $value = "";
    $bind_types  = "";
    $parameters  = [];
    $b   = 0;
    $sep = "";
    $where = "";

    $UpdateStmt  = new UpdateStatement();

    foreach($tb_columns as $fname => $details){ // Check the customer received. If blank insert blank into the array.
      if($details["update"]){
        if(!array_key_exists($fname, $fields_data)) {
          $value = '';
        }else{
          $value = $fields_data[$fname];
        }
        if(array_key_exists('password', $details) && $details["password"]){
          $value = md5($value);
        }

        $columns = $columns.$fname."=?,";
        $bind_types .= $details["bind_type"];
        $parameters[$b++] = $value;

      }elseif(array_key_exists("default",$details) and array_key_exists("fill_on_update", $details["default"])){
        if($details["default"]["type"] == "function"){//MySQL function call

          $columns = $columns.$fname."=?,";
          $bind_types .= $details["bind_type"];
          $parameters[$b++] = $details["default"]["value"];

        }elseif($details["default"]["type"] == "token_id"){//JSON WebToken Id
          $token = Token::getInstance();
          if($token->getId()!=null){
            $columns = $columns.$fname."=?,";
            $bind_types .= $details["bind_type"];
            $parameters[$b++] = $token->getId();
          }

        }elseif($details["default"]["type"] == "value"){//Fixed Value

          $columns = $columns.$fname."=?,";
          $bind_types .= $details["bind_type"];
          $parameters[$b++] = $details["default"]["value"];
        }
        elseif($details["default"]["type"] == "variable"){//Fixed Value

          $columns = $columns.$fname."=?,";
          $bind_types .= $details["bind_type"];
          $parameters[$b++] = $details["default"]["value"];
        }
      }

    }


    $WhereCond   = new WHERECondition($tb_columns, $keys, $parms);

    $UpdateStmt->setColumns(trim($columns,','));
    $UpdateStmt->setTbName($tb_name);
    $UpdateStmt->setWHERECond($WhereCond);
    $UpdateStmt->setBindTypes($bind_types);
    $UpdateStmt->setParameters($parameters);
    $UpdateStmt->buildStmt(); //Builds Select Statement

    return $UpdateStmt;

  }




  public static function prepareDeleteStmtParts( $tb_name, $tb_columns, $keys, $parms){

    $DeleteStmt  = new DeleteStatement();

    $WhereCond   = new WHERECondition($tb_columns, $keys, $parms);

    if(trim($WhereCond->getWHERE())!==""){
      $DeleteStmt->setTbName($tb_name);
      $DeleteStmt->setWHERECond($WhereCond);
      $DeleteStmt->buildStmt(); //Builds Select Statement
      return $DeleteStmt;
    }else{
      throw new Exception('Delete without condition not allowed!');
    }

  }

}





abstract class SQLStatement{
  protected $dbcon;
  protected $dbi;
  protected $statement;
  protected $tb_name;
  protected $columns;
  protected $values;
  protected $WHERECond;
  protected $order;
  protected $parameters;
  protected $bind_types;

  public function setStatement($stmt){
    $this->statement = $stmt;
  }
  public function getStatement(){
    return $this->statement;
  }

  public function setTbName($tb_name){
    $this->tb_name = $tb_name;
  }
  public function getTbName(){
    return $this->tb_name;
  }

  public function setColumns($columns){
    $this->columns = $columns;
  }
  public function getColumns(){
    return $this->columns;
  }

  public function setValues($values){
    $this->values = $values;
  }
  public function getValues(){
    return $this->values;
  }


  public function setWHERECond($where){
    $this->WHERECond = $where;
  }

  public function getWHERECond(){
    return $this->WHERECond;
  }

  public function setOrder($order){
    $this->order = $order;
  }

  public function getOrder(){
    return $this->order;
  }

  public function setParameters($parameters){
    $this->parameters = $parameters;
  }

  public function getParameters(){
    return $this->parameters;
  }


  public function setBindTypes($bind_types){
    $this->bind_types = $bind_types;
  }

  public function getBindTypes(){
    return $this->bind_types;
  }

  public function buildStmt(){
    /*To be implemented*/
  }

}


/*
class SelectionElement{
  private $field;
  private $operation;
  private $alias;

  public function __construct(){
    $this->field = null;
    $this->operation = null;
    $this->alias = null;
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
    if($this->field!=null and trim($this->field)!==""){
      return true;
    }else{
      return false;
    }
  }
}
* /



class SelectStatement extends SQLStatement{

  const ALL_RECORDS = "ALL";
  const FIRST_ONLY  = "FIRST";

  public function __construct(){

    $this->dbcon = DBFactory::getInstance()->getDB();
    $this->dbi = $this->dbcon->getMysqli();

    $this->stmt = "";
    $this->columns = "";
    $this->tb_name = "";
    //$this->WHERECond = new WHERECondition();
    $this->order = "";

  }

  public function buildStmt(){
    $this->stmt = "SELECT " . $this->columns . " FROM " . $this->tb_name;

    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $this->stmt .= " WHERE " . $this->WHERECond->getWHERE();
    }

    if(trim($this->order)!==""){
      $this->stmt .= " ORDER BY ".$this->order;
    }

  }

  public function execute($return_mode){
    $result = array();
    $bind_types = $this->WHERECond->getBindTypes();
    $parameters = $this->WHERECond->getParameters();

    /* Prepare statement * /
    $prep_stmt = $this->dbi->prepare($this->stmt);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->stmt . ' Error: ' . $this->dbi->error.__LINE__ );
    }
    else{
      if(trim($bind_types)!==""){
        $bind_parms = [];
        $bind_parms[] = & $bind_types;
        for($i = 0; $i < count($parameters); $i++){
          $bind_parms[] = & $parameters[$i];
        }
        call_user_func_array(array($prep_stmt, 'bind_param'), $bind_parms);
      }

      /* Execute statement * /
      $prep_stmt->execute() or die($this->dbi->error.__LINE__);

      /* Fetch result to array * /
      $r = $prep_stmt->get_result();

      if($r->num_rows > 0){
        if($return_mode == self::ALL_RECORDS ){
          while($row = $r->fetch_assoc()){
            $result[] = $row;
          }
        }else{
          $result = $r->fetch_assoc();
        }
      }
    }
    return $result;
  }

}





class UpdateStatement extends SQLStatement{

  public function __construct(){

    $this->dbcon = DBFactory::getInstance()->getDB();
    $this->dbi = $this->dbcon->getMysqli();

    $this->stmt = "";
    $this->columns = "";
    $this->tb_name = "";
    $this->WHERECond = new WHERECondition();

  }

  public function buildStmt(){
    $this->stmt = "UPDATE " . $this->tb_name . " SET " . $this->columns;
    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $this->stmt .= " WHERE " . $this->WHERECond->getWHERE();
    }
  }

  public function execute(){
    $result = 0;

    $bind_types = $this->bind_types . $this->WHERECond->getBindTypes();
    $parameters = $this->parameters;
    if(sizeof($this->WHERECond->getParameters())>0){
      $b = sizeof($parameters);
      for($i=0;$i<sizeof($this->WHERECond->getParameters());$i++){
        $parameters[$b++] = $this->WHERECond->getParameters()[$i];
      }
    }


    /* Prepare statement * /
    $prep_stmt = $this->dbi->prepare($this->stmt);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->stmt . ' Error: ' . $this->dbi->error.__LINE__ );
    }
    else{
      if(trim($bind_types)!==""){
        $bind_parms = [];
        $bind_parms[] = & $bind_types;
        for($i = 0; $i < count($parameters); $i++){
          $bind_parms[] = & $parameters[$i];
        }
        call_user_func_array(array($prep_stmt, 'bind_param'), $bind_parms);
      }

      /* Execute statement * /
      $prep_stmt->execute() or die($this->dbi->error.__LINE__);

      /* Get number of lines affected * /
      $result = $prep_stmt->affected_rows;
    }
    return $result;
  }

}





class InsertStatement extends SQLStatement{

  public function __construct(){

    $this->dbcon = DBFactory::getInstance()->getDB();
    $this->dbi = $this->dbcon->getMysqli();

    $this->stmt = "";
    $this->columns = "";
    $this->values = "";
    $this->tb_name = "";

  }

  public function buildStmt(){

    $this->stmt = "INSERT INTO ". $this->tb_name ."(".$this->columns.") VALUES(".$this->values.")";

  }

  public function execute(){
    $result = 0;

    $bind_types = $this->bind_types;
    $parameters = $this->parameters;

    /* Prepare statement * /
    $prep_stmt = $this->dbi->prepare($this->stmt);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->stmt . ' Error: ' . $this->dbi->error.__LINE__ );
    }
    else{
      if(trim($bind_types)!==""){
        $bind_parms = [];
        $bind_parms[] = & $bind_types;
        for($i = 0; $i < count($parameters); $i++){
          $bind_parms[] = & $parameters[$i];
        }
        call_user_func_array(array($prep_stmt, 'bind_param'), $bind_parms);
      }

      /* Execute statement * /
      $prep_stmt->execute() or die($this->dbi->error.__LINE__);

      /* Get number of lines affected * /
      $result = $prep_stmt->affected_rows;
    }
    return $result;
  }

}




class DeleteStatement extends SQLStatement{

  public function __construct(){

    $this->dbcon = DBFactory::getInstance()->getDB();
    $this->dbi = $this->dbcon->getMysqli();

    $this->stmt = "";
    $this->tb_name = "";
    $this->WHERECond = new WHERECondition();

  }

  public function buildStmt(){

    $this->stmt = "DELETE FROM " . $this->tb_name;
    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $this->stmt .= " WHERE " . $this->WHERECond->getWHERE();
    }

  }

  public function execute(){
    $result = 0;
    $bind_types = $this->WHERECond->getBindTypes();
    $parameters = $this->WHERECond->getParameters();

    /* Prepare statement * /
    $prep_stmt = $this->dbi->prepare($this->stmt);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->stmt . ' Error: ' . $this->dbi->error.__LINE__ );
    }
    else{
      if(trim($bind_types)!==""){
        $bind_parms = [];
        $bind_parms[] = & $bind_types;
        for($i = 0; $i < count($parameters); $i++){
          $bind_parms[] = & $parameters[$i];
        }
        call_user_func_array(array($prep_stmt, 'bind_param'), $bind_parms);
      }

      /* Execute statement * /
      $prep_stmt->execute() or die($this->dbi->error.__LINE__);

      /* Get number of lines affected * /
      $result = $prep_stmt->affected_rows;
    }
    return $result;
  }

}




class WHERECondition{
  private $WHERE;
  private $parameters;
  private $bind_types;
  private $tb_columns;
  private $keys;
  private $values;

  public function __construct( $tb_columns = null, $keys = null, $values = null){
    if($tb_columns!=null and $keys!=null and $values !=null){
      $this->tb_columns = $tb_columns;
      $this->keys       = $keys;
      $this->values     = $values;
      $this->build();
    }
  }

  public function parseConditionElements($tb_columns, $conditionElements){
    $this->tb_columns = $tb_columns;
    $this->WHERE      = "";
    $this->parameters = [];
    $this->bind_types = "";
    $idx = 0;
    foreach($conditionElements as $condElement){
      if($condElement->isFieldCriteria()){
        if($this->tb_columns->exists($condElement->getField())){
          $this->bind_types .= $tb_columns->getTableColumn($condElement->getField())->getFieldDetails()->getBindType();//[$condElement->getField()]["bind_type"];//
          $this->parameters[$idx++] = $condElement->getValue();
          $this->WHERE = $this->WHERE . " " . $condElement->getField() ." = ?";

        }else{
          throw new Exception("Field '" . $this->keys[$idx] . "' not found in model specification");
        }
      }else{
        $this->WHERE = $this->WHERE . " " . $condElement->getOperator();
      }
    }

  }

  public function build(){
      $this->WHERE      = "";
      $this->parameters = [];
      $this->bind_types = "";
      $sep  = "";

      if(sizeof($this->keys)>0){
        $values_local = array();
        if($this->values){
          if(sizeof($this->keys)>sizeof($this->values)){
            foreach($this->values as $value){
              $values_exp = explode( ",", $value );
              foreach($values_exp as $value_exp){
                $values_local[] = $value_exp;
              }
            }

          }else {
            $values_local = $this->values;
          }

          $idx = 0;
          foreach($values_local as $value){
            if(array_key_exists($this->keys[$idx], $this->tb_columns)){
              $this->bind_types .= $this->tb_columns[$this->keys[$idx]]["bind_type"];
              $this->parameters[$idx] = $value;
              $this->WHERE = $this->WHERE . $sep . $this->keys[$idx++] ." = ?";
              $sep = " AND ";
            }else{
              throw new Exception("Field '" . $this->keys[$idx] . "' not found in model specification");
            }
          }

        }
      }

  }

  public function setWHERE($where){
    $this->WHERE = $where;
  }

  public function getWHERE(){
    return $this->WHERE;
  }

  public function setParameters($parameters){
    $this->parameters = $parameters;
  }

  public function getParameters(){
    return $this->parameters;
  }

  public function setBindTypes($bind_types){
    $this->bind_types = $bind_types;
  }

  public function getBindTypes(){
    return $this->bind_types;
  }

  public function setTbColumns($tb_columns){
    $this->tb_columns = $tb_columns;
  }
  public function getTbColumns(){
    return $this->tb_columns;
  }

  public function setKeys($keys){
    $this->keys = $keys;
  }
  public function getKeys(){
    return $this->keys;
  }

  public function setValues($values){
    $this->values = $values;
  }
  public function getValues(){
    return $this->values;
  }

}*/
?>
