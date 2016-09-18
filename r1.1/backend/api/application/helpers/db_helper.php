<?php
namespace com\mercuryfw\helpers;
interface if_db{

  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   */
  public static function getInstance($conn_data);


  public function prefixDB();

  public function prefixTB();

  public function getDBName();

  public function getSelectStatement($tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements);

  public function getInsertStatement( $tb_name, $InsertionElements );

  public function getUpdateStatement($tb_name, $UpdateElements, $WhereConditionElements);

  public function getDeleteStatement($tb_name, $WhereConditionElements);

}

class dbFactory{


  private $db_data;
  private $DB_Config;

  /**
   * @var Singleton The reference to *Singleton* instance of this class
   */
  private static $instance;


  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   */
  public static function getInstance(){

      if (null === static::$instance) {
          static::$instance = new static();
      }

      return static::$instance;
  }

  /**
   * Protected constructor to prevent creating a new instance of the
   * *Singleton* via the `new` operator from outside of this class.
   */
  protected function __construct(){
    if($this->db_data == null){
      //$this->db_data = include( __ROOT__."/backend/config/database.php" );
      $this->DB_Config = json_decode(file_get_contents(__ROOT__.'/backend/config/databases.json'), true); //Getting database(s) config data
    }
  }

  public function getDB($db_cfg_name){
    if(array_key_exists($db_cfg_name, $this->DB_Config)){
      if($this->DB_Config[$db_cfg_name]["DB_TYPE"]=='mysql'){
        return mySqlDB::getInstance($this->DB_Config[$db_cfg_name]); //$this->db_data);
      }else{
        throw new Exception("Database Type ".$this->DB_Config[$db_cfg_name]["DB_TYPE"]." not supported yet!");
      }
    }else{
      throw new Exception("Database Config ".$db_cfg_name." not found!");
    }
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

}

class mySqlDB implements if_db{

    public $mysqli = null;
    private $conn_data = null;
    private $dbName = "";
    private $prefix_db = false;
    private $prefix_tb = false;


    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance($conn_data){

        if (null === static::$instance) {
            static::$instance = new static($conn_data);
        }

        return static::$instance;
    }

    public function getMysqli(){

      if($this->mysqli==null){
        $this->mysqli = new \mysqli($this->conn_data['DB_SERVER'], $this->conn_data['DB_USER'], $this->conn_data['DB_PASSWORD'], $this->conn_data['DB']);
      }

      return $this->mysqli;

    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($conn_data){
      if($this->conn_data == null){
        $this->conn_data = $conn_data; //include( __ROOT__."/backend/api/application/config/database.php" );
        $this->prefix_db = $this->conn_data['PREFIX_DB'];
        $this->prefix_tb = $this->conn_data['PREFIX_TB'];
        $this->dbName = $this->conn_data['DB'];
      }
    }

    public function prefixDB(){
      return $this->prefix_db;
    }

    public function prefixTB(){
      return $this->prefix_tb;
    }

    public function getDBName(){
      return $this->dbName;
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


    public function getSelectStatement($tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements){
      $stmt = new mySQLSelectStatement($this, $tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getInsertStatement( $tb_name, $InsertionElements ){
      $stmt = new mySQLInsertStatement($this, $tb_name, $InsertionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getUpdateStatement($tb_name, $UpdateElements, $WhereConditionElements){
      $stmt = new mySQLUpdateStatement($this, $tb_name, $UpdateElements, $WhereConditionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getDeleteStatement($tb_name, $WhereConditionElements){
      $stmt = new mySQLDeleteStatement($this, $tb_name, $WhereConditionElements);
      $stmt->buildStmt();
      return $stmt;
    }
}


abstract class mySQLStatement{
  protected $dbcon;
  protected $statement;
  protected $tb_name;
  protected $selectionElements;
  protected $whereConditionElements;
  protected $selectionOrderElements;
  protected $columns;
  protected $values;
  protected $WHERECond;
  protected $order;
  protected $bind_types;
  protected $bind_parms;

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

  public function setSelectionElements($selelems){
    $this->selectionElements = $selelems;
  }
  public function getSelectionElements(){
    return $this->selectionElements;
  }

  public function setWhereConditionElements($whereelems){
    $this->whereConditionElements = $whereelems;
  }
  public function getWhereConditionElements(){
    return $this->whereConditionElements;
  }

  public function setSelectionOrderElements($orderelems){
    $this->selectionOrderElements = $orderelems;
  }
  public function getSelectionOrderElements(){
    return $this->selectionOrderElements;
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

  protected function prepareSelectionColumns($selectionElements){
    $this->selectionElements = $selectionElements;
    $this->columns = "";
    $sep = '';
    foreach($this->selectionElements as $field){
      $fieldToSel = $field->getField()->getName();
      if($field->getOperation()!=null){
        $fieldToSel = $field->getOperation() .'('.$fieldToSel.')';
      }
      if($field->getAlias()!=null){
        $fieldToSel .= ' as ' . $field->getAlias();
      }
      $this->columns .= $sep . $fieldToSel;
      $sep = ', ';
    }
  }

  protected function prepareSelectionOrder($selectionOrderElements){
    $this->selectionOrderElements = $selectionOrderElements;
    $this->order = null;
    $sep = '';
    if($this->selectionOrderElements!=null){
      $this->order = "";
      foreach($this->selectionOrderElements as $field){
        $this->order .= $sep . $field->getField()->getName() . ' ' . $field->getOrder();
        $sep = ', ';
      }
    }
  }

  protected function prepareWhereCondition($whereConditionElements){
    $this->whereConditionElements = $whereConditionElements;
    $this->WHERECond = null;
    $sep = '';
    if($this->whereConditionElements!=null){
      $this->WHERECond = new mySQLWHERECondition($this->whereConditionElements);
    }
  }

  protected function prepareInsertionColumns($insertionElements){
    $this->insertionElements = $insertionElements;
    $this->columns = "";
    $this->values = "";
    $this->bind_types = "";
    $this->bind_parms = [];
    $sep = '';

    foreach($this->insertionElements as $field){
      $this->columns .= $sep . $field->getField()->getName();
      if(strtoupper($field->getValue())=="CURRENT_TIMESTAMP"){
        $this->values  .= $sep . 'CURRENT_TIMESTAMP';
      }else{
        $this->values  .= $sep . '?';
        $this->bind_types .= $field->getField()->getBindType();
        $this->bind_parms[] = $field->getValue();
      }
      $sep = ', ';
    }
  }

  protected function prepareUpdateColumns($updateElements){
    $this->updateElements = $updateElements;
    $this->columns = "";
    $this->bind_types = "";
    $this->bind_parms = [];
    $sep = '';

    foreach($this->updateElements as $field){
      if(strtoupper($field->getValue())=="CURRENT_TIMESTAMP"){
        $this->columns .= $sep . $field->getField()->getName() .'=CURRENT_TIMESTAMP';
      }else{
        $this->columns .= $sep . $field->getField()->getName() .'=?';
        $this->bind_types .= $field->getField()->getBindType();
        $this->bind_parms[] = $field->getValue();
      }
      $sep = ', ';
    }
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

  public function setBindParms($parameters){
    $this->bind_parms = $parameters;
  }

  public function getBindParms(){
    return $this->bind_parms;
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





class mySQLWHERECondition{
  private $WHERE;
  private $parameters;
  private $bind_types;

  public function __construct( $conditionElements = null){
    if($conditionElements !=null){
      $this->parseConditionElements($conditionElements);
    }
  }

  public function parseConditionElements($conditionElements){

    $this->WHERE      = "";
    $this->parameters = [];
    $this->bind_types = "";
    $idx = 0;
    foreach($conditionElements as $condElement){
      if($condElement->isFieldCriteria()){
        $this->bind_types .= $condElement->getField()->getBindType();
        $this->parameters[$idx++] = $condElement->getValue();
        $this->WHERE = $this->WHERE . " " . $condElement->getField()->getName() ." = ?";
      }else{
        $this->WHERE = $this->WHERE . " " . $condElement->getOperator();
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

}





class mySQLSelectStatement extends mySQLStatement{

  const ALL_RECORDS = "ALL";
  const FIRST_ONLY  = "FIRST";

  public function __construct($dbcon, $tb_name = null, $selectionElements = null, $whereConditionElements = null, $selectionOrderElements = null){

    $this->statement = "";
    $this->columns = "";
    $this->tb_name = "";
    $this->order = "";

    $this->dbcon = $dbcon;
    $this->tb_name = $tb_name;
    if($selectionElements!=null){
      $this->prepareSelectionColumns($selectionElements);
    }

    if($whereConditionElements!=null){
      $this->prepareWhereCondition($whereConditionElements);
    }
    if($selectionOrderElements!=null){
      $this->prepareSelectionOrder($selectionOrderElements);
    }

  }

  public function buildStmt(){

    $this->statement = "SELECT " . $this->columns . " FROM " . ($this->dbcon->prefixDB()?$this->dbcon->getDBName().".":"") . $this->tb_name;

    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $this->statement .= " WHERE " . $this->WHERECond->getWHERE();
    }

    if(trim($this->order)!==""){
      $this->statement .= " ORDER BY ".$this->order;
    }

  }

  public function execute($return_mode=self::ALL_RECORDS){
    $result = array();
    $bind_types = "";
    $parameters = [];

    if($this->WHERECond!=null){
      $bind_types = $this->WHERECond->getBindTypes();
      $parameters = $this->WHERECond->getParameters();
    }

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getMysqli()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getMysqli()->error.__LINE__ );
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

      /* Execute statement */
      $prep_stmt->execute() or die($this->dbcon->getMysqli()->error.__LINE__);

      /* Fetch result to array */
      if(method_exists($prep_stmt,'get_result')){
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
      }else{

        $prep_stmt->store_result();
        //custom class :D bind to Statement Result mambo jambo!
        $sr = new Statement_Result($prep_stmt);

        if($prep_stmt->num_rows > 0){
          if($return_mode == self::ALL_RECORDS ){
            $result = [];

            for($i=0;$i<$prep_stmt->num_rows;$i++){

              $result[] = $sr->Get_Row($prep_stmt);
              $prep_stmt->fetch();

            }

          }
          else{

            $result = $sr->Get_Row($prep_stmt);
            $prep_stmt->fetch();

          }

        }
      }

      /*$r = $prep_stmt->get_result();

      if($r->num_rows > 0){
        if($return_mode == self::ALL_RECORDS ){
          while($row = $r->fetch_assoc()){
            $result[] = $row;
          }
        }else{
          $result = $r->fetch_assoc();
        }
      }*/
    }
    return $result;
  }

}





class mySQLInsertStatement extends mySQLStatement{

  public function __construct($dbcon, $tb_name = null, $insertionElements = null){

    $this->statement = "";
    $this->columns = "";
    $this->values = "";
    $this->bind_types = "";
    $this->bind_parms = [];

    $this->dbcon = $dbcon;
    $this->tb_name = $tb_name;
    if($insertionElements!=null){
      $this->prepareInsertionColumns($insertionElements);
    }


  }

  public function buildStmt(){

    $this->statement = "INSERT INTO ". ($this->dbcon->prefixDB()?$this->dbcon->getDBName().".":"") . $this->tb_name ."(".$this->columns.") VALUES(".$this->values.")";

  }

  public function execute(){

    $result = 0;
    $bind_types = $this->bind_types;
    $parameters = $this->bind_parms;

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getMysqli()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getMysqli()->error.__LINE__ );
    }
    else{
      if(trim($this->bind_types)!==""){
        $bind_parms = [];
        $bind_parms[] = & $bind_types;
        for($i=0;$i < sizeof($parameters);$i++){
          $bind_parms[] = & $parameters[$i];
        }
        call_user_func_array(array($prep_stmt, 'bind_param'), $bind_parms);
      }

      /* Execute statement */
      $prep_stmt->execute() or die($this->dbcon->getMysqli()->error.__LINE__);

      /* Get number of lines affected */
      $result = $prep_stmt->affected_rows;

    }
    return $result;
  }

}




class mySQLUpdateStatement extends mySQLStatement{


  public function __construct($dbcon, $tb_name = null, $updateElements = null, $whereConditionElements = null){

    $this->statement = "";
    $this->columns = "";
    $this->tb_name = "";

    $this->dbcon = $dbcon;
    $this->tb_name = $tb_name;
    if($updateElements!=null){
      $this->prepareUpdateColumns($updateElements);
    }

    if($whereConditionElements!=null){
      $this->prepareWhereCondition($whereConditionElements);
    }else{
      throw new Exception('Update without condition not allowed!');
    }

  }

  public function buildStmt(){

    $this->statement = "UPDATE " . ($this->dbcon->prefixDB()?$this->dbcon->getDBName().".":"") . $this->tb_name . " SET " . $this->columns;

    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $this->statement .= " WHERE " . $this->WHERECond->getWHERE();
    }

  }

  public function execute(){
    $result = 0;
    $bind_types = "";
    $parameters = [];

    $bind_types = $this->bind_types;
    $parameters = $this->bind_parms;

    if(sizeof($this->WHERECond->getParameters())>0){
      $bind_types .= $this->WHERECond->getBindTypes();
      $b = sizeof($parameters);
      for($i=0;$i<sizeof($this->WHERECond->getParameters());$i++){
        $parameters[$b++] = $this->WHERECond->getParameters()[$i];
      }
    }


    /* Prepare statement */
    $prep_stmt = $this->dbcon->getMysqli()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getMysqli()->error.__LINE__ );
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

      /* Execute statement */
      $prep_stmt->execute() or die($this->dbcon->getMysqli()->error.__LINE__);

      /* Get number of lines affected */
      $result = $prep_stmt->affected_rows;

    }
    return $result;
  }

}





class mySQLDeleteStatement extends mySQLStatement{


  public function __construct($dbcon, $tb_name = null, $whereConditionElements = null){

    $this->statement = "";
    $this->tb_name = "";

    $this->dbcon = $dbcon;
    $this->tb_name = $tb_name;

    if($whereConditionElements!=null){
      $this->prepareWhereCondition($whereConditionElements);
    }else{
      throw new Exception('Delete without condition not allowed!');
    }

  }

  public function buildStmt(){

    $this->statement = "DELETE FROM " . ($this->dbcon->prefixDB()?$this->dbcon->getDBName().".":"") . $this->tb_name;

    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $this->statement .= " WHERE " . $this->WHERECond->getWHERE();
    }else{
      throw new Exception('Delete without condition not allowed!');
    }

  }

  public function execute(){
    $result = 0;
    $bind_types = "";
    $parameters = [];

    $bind_types = $this->bind_types;
    $parameters = $this->bind_parms;
    if(sizeof($this->WHERECond->getParameters())>0){
      $bind_types = $this->WHERECond->getBindTypes();
      $parameters = $this->WHERECond->getParameters();
    }


    /* Prepare statement */
    $prep_stmt = $this->dbcon->getMysqli()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getMysqli()->error.__LINE__ );
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

      /* Execute statement */
      $prep_stmt->execute() or die($this->dbcon->getMysqli()->error.__LINE__);

      /* Get number of lines affected */
      $result = $prep_stmt->affected_rows;

    }
    return $result;
  }

}




class Statement_Result
{
    private $_bindVarsArray = array();
    private $_results = array();
    private $meta;

    public function __construct(&$stmt)
    {
        /*$this->meta = $stmt->result_metadata();

        while ($columnName = $this->meta->fetch_field())
            $this->_bindVarsArray[] = &$this->_results[$columnName->name];

        call_user_func_array(array($stmt, 'bind_result'), $this->_bindVarsArray);

        $meta->close();*/
    }

    public function Get_Row(&$stmt){
      $this->_results = [];
      $this->_bindVarsArray = [];
      $this->meta = $stmt->result_metadata();
      while ($columnName = $this->meta->fetch_field())
          $this->_bindVarsArray[] = &$this->_results[$columnName->name];


      call_user_func_array(array($stmt, 'bind_result'), $this->_bindVarsArray);

      return $this->Get_Array();

    }

    public function Get_Array()
    {
        return $this->_results;
    }

    public function Get($column_name)
    {
        return $this->_results[$column_name];
    }
}

 ?>
