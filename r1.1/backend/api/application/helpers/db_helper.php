<?php
namespace com\mercuryfw\helpers;
use \Exception as Exception;
use \mysqli as mysqli;
use \PDO as PDO;
use com\mercuryfw\models\configModel as configModel;
use com\mercuryfw\helpers\Logger as Logger;

interface if_db{

  /**
   * Returns the *Singleton* instance of this class.
   *
   * @return Singleton The *Singleton* instance.
   */
  public static function getInstance($conn_data, $logger_instance);

  public function prefixDB();

  public function prefixTB();

  public function getDBName();

  public function getLogger();

  public function getSelectStatement( $tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements);

  public function getInsertStatement( $tb_name, $InsertionElements );

  public function getUpdateStatement( $tb_name, $UpdateElements, $WhereConditionElements);

  public function getDeleteStatement( $tb_name, $WhereConditionElements);

  public function getTablesListStatement();

  public function getTableStructureStatement( $tbName );

  public function getDBConn();

}

class dbFactory{


  private $db_data;
  private $DB_Config;
  private $logger;

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
      $this->logger = Logger::getInstance();
      $dbModel = new configModel("databases");
      $this->DB_Config = $dbModel->listAll();//json_decode(file_get_contents(__ROOT__.'/backend/config/databases.json'), true); //Getting database(s) config data
    }
  }

  public function getDB($db_cfg_name){
    if(array_key_exists($db_cfg_name, $this->DB_Config)){
      if($this->DB_Config[$db_cfg_name]["DB_TYPE"]=='mysql'){
        return mySqlDB::getInstance($this->DB_Config[$db_cfg_name], $this->logger);
      }elseif($this->DB_Config[$db_cfg_name]["DB_TYPE"]=='mysql_PDO'){
        return mySql_PDO_DB::getInstance($this->DB_Config[$db_cfg_name], $this->logger);
      }elseif($this->DB_Config[$db_cfg_name]["DB_TYPE"]=='pgsql_PDO'){
        return postGreSql_PDO_DB::getInstance($this->DB_Config[$db_cfg_name], $this->logger);
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

    private $logger;
    public  $mysqli = null;
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
    public static function getInstance($conn_data, $logger_instance){

        if (null === static::$instance) {
          static::$instance = new static($conn_data, $logger_instance);
        }

        return static::$instance;
    }

    public function getDBConn(){

      if($this->mysqli==null){
        $this->mysqli = new mysqli($this->conn_data['DB_SERVER'], $this->conn_data['DB_USER'], $this->conn_data['DB_PASSWORD'], $this->conn_data['DB']);
      }

      return $this->mysqli;

    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($conn_data, $logger_instance){
      $this->logger = $logger_instance;
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

    public function getLogger(){
      return $this->logger;
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


    public function getSelectStatement($tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements, $max_recs_select=100){
      $stmt = new mySQLSelectStatement($this, $tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements, $max_recs_select);
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

    public function getTablesListStatement(){
      $stmt = new mySQLGetTableListStatement($this);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getTableStructureStatement($tbName){
      $stmt = new mySQLGetTableStructureStatement($this, $tbName);
      $stmt->buildStmt();
      return $stmt;
    }
}


class mySql_PDO_DB implements if_db{

    private $logger;
    public  $dbconn = null;
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
    public static function getInstance($conn_data, $logger_instance){

        if (null === static::$instance) {
          static::$instance = new static($conn_data, $logger_instance);
        }

        return static::$instance;
    }

    public function getDBConn(){

      if($this->dbconn==null){
        $this->dbconn = new PDO('mysql:host='.$this->conn_data['DB_SERVER'].';dbname='.$this->conn_data['DB'].';charset=utf8mb4',
                                $this->conn_data['DB_USER'],
                                $this->conn_data['DB_PASSWORD'],
                                array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

      }

      return $this->dbconn;

    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($conn_data, $logger_instance){
      $this->logger = $logger_instance;
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

    public function getLogger(){
      return $this->logger;
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


    public function getSelectStatement($tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements, $max_recs_select=100){
      $stmt = new mySQL_PDO_SelectStatement($this, $tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements, $max_recs_select);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getInsertStatement( $tb_name, $InsertionElements ){
      $stmt = new mySQL_PDO_InsertStatement($this, $tb_name, $InsertionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getUpdateStatement($tb_name, $UpdateElements, $WhereConditionElements){
      $stmt = new mySQL_PDO_UpdateStatement($this, $tb_name, $UpdateElements, $WhereConditionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getDeleteStatement($tb_name, $WhereConditionElements){
      $stmt = new mySQL_PDO_DeleteStatement($this, $tb_name, $WhereConditionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getTablesListStatement(){
      $stmt = new mySQL_PDO_GetTableListStatement($this);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getTableStructureStatement($tbName){
      $stmt = new mySQL_PDO_GetTableStructureStatement($this, $tbName);
      $stmt->buildStmt();
      return $stmt;
    }
}

class postGreSql_PDO_DB implements if_db{

    private $logger;
    public  $dbconn = null;
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
    public static function getInstance($conn_data, $logger_instance){

        if (null === static::$instance) {
            static::$instance = new static($conn_data, $logger_instance);
        }

        return static::$instance;
    }

    public function getDBConn(){

      if($this->dbconn==null){
        $this->dbconn = new PDO('pgsql:dbname='.$this->conn_data['DB'].';host='.$this->conn_data['DB_SERVER'].'',
                                 $this->conn_data['DB_USER'],
                                 $this->conn_data['DB_PASSWORD']);
      }

      return $this->dbconn;

    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct($conn_data, $logger_instance){
      $this->logger = $logger_instance;
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

    public function getDBUser(){
      return $this->conn_data['DB_USER'];
    }

    public function getLogger(){
      return $this->logger;
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


    public function getSelectStatement($tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements, $max_recs_select=100){
      $stmt = new pgSQL_PDO_SelectStatement($this, $tb_name, $SelectionElements, $WhereConditionElements, $SelectionOrderElements, $max_recs_select);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getInsertStatement( $tb_name, $InsertionElements ){
      $stmt = new mySQL_PDO_InsertStatement($this, $tb_name, $InsertionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getUpdateStatement($tb_name, $UpdateElements, $WhereConditionElements){
      $stmt = new mySQL_PDO_UpdateStatement($this, $tb_name, $UpdateElements, $WhereConditionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getDeleteStatement($tb_name, $WhereConditionElements){
      $stmt = new mySQL_PDO_DeleteStatement($this, $tb_name, $WhereConditionElements);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getTablesListStatement(){
      $stmt = new pgSQL_PDO_GetTableListStatement($this);
      $stmt->buildStmt();
      return $stmt;
    }

    public function getTableStructureStatement($tbName){
      $stmt = new pgSQL_PDO_GetTableStructureStatement($this, $tbName);
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
  protected $pagination_config;
  protected $max_recs_select;

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

  public function setMaxRecsSelect($data){
    $this->max_recs_select = $data;
  }

  public function getMaxRecsSelect(){
    return $this->max_recs_select;
  }


  public function preparePagination($pagination){
    $this->pagination_config = [];
    $mode__per_page = false;
    $mode__per_range = false;
    $offset = 0;
    $page = 1;
    $per_page = $this->getMaxRecsSelect();
    if(sizeOf($pagination)>0){
      for($i=0;$i<sizeOf($pagination);$i++){
        if($pagination[$i]->getName() == '_page'){
          $page = $pagination[$i]->getValue();
          $mode__per_page = true;
        }elseif($pagination[$i]->getName() == '_per_page'){
          $per_page = $pagination[$i]->getValue();
          $mode__per_page = true;
        }elseif($pagination[$i]->getName() == '_range'){
          $page = '*';
          $offset = $pagination[$i]->getValueLow();
          $per_page = 1 + $pagination[$i]->getValueHigh() - $pagination[$i]->getValueLow();
          $mode__per_range = true;
        }
      }
      if($mode__per_page && $mode__per_range){
        throw new Exception('Please use only one way of pagination(_range or _page + _per_page)!');
      }elseif($mode__per_page){
        $offset = ( ($page - 1) * $per_page );
      }
    }
    $this->pagination_config['page'] = $page;
    $this->pagination_config['offset'] = $offset;
    $this->pagination_config['per_page'] = $per_page;
  }

  public function addPagination($pagination){
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
        $value = $condElement->getValue();
        if(strtolower(trim($condElement->getOperator()))=='like'){
          $value = str_replace("*", "%", $value);
        }
        $this->parameters[$idx++] = $value;
        $this->WHERE = $this->WHERE . " " . $condElement->getField()->getName() ." ".$condElement->getOperator()." ?";
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


class mySQLGetTableListStatement extends mySQLStatement{
  public function __construct($dbcon){
    $this->dbcon = $dbcon;
    $this->statement = "show full tables from ".$dbcon->getDBName();
  }

  public function execute(){

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, null, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $result = [];

    /* Prepare statement */
    if($r = $this->dbcon->getDBConn()->query($this->statement)){
      while($row = $r->fetch_assoc()){
        $result[] = $row;
      }
      /* Fetch result to array * /
      if(method_exists($stmt,'get_result')){
        $r = $stmt->get_result();

        if($r->num_rows > 0){
          while($row = $r->fetch_assoc()){
            $result[] = $row;
          }
        }
      }else{

        $stmt->store_result();
        //custom class :D bind to Statement Result mambo jambo!
        $sr = new Statement_Result($stmt);

        if($stmt->num_rows > 0){

          $result = [];

          for($i=0;$i<$prep_stmt->num_rows;$i++){

            $result[] = $sr->Get_Row($prep_stmt);
            $prep_stmt->fetch();

          }

        }
      }*/
    }
    return $result;
  }


}


class mySQL_PDO_GetTableListStatement extends mySQLStatement{
  public function __construct($dbcon){
    $this->dbcon = $dbcon;
    $this->statement = "SHOW FULL TABLES"; //.$dbcon->getDBName()
  }

  public function execute(){

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, null, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $result = [];

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */
      $prep_stmt->execute();


      /* Fetch result to array */
      $result = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    return $result;
  }


}

class pgSQL_PDO_GetTableListStatement extends mySQLStatement{
  public function __construct($dbcon){
    $this->dbcon = $dbcon;
    $this->statement = "SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = ?"; //.$dbcon->getDBName()
  }

  public function execute(){

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, null, "SQL Statement: ".$this->statement." Schema:".$this->dbcon->getDBUser() ); //Logging SQL Statement

    $result = [];

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */
      $prep_stmt->execute([$this->dbcon->getDBUser()]);


      /* Fetch result to array */
      $result = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    return $result;
  }


}



class mySQLGetTableStructureStatement extends mySQLStatement{
  public function __construct($dbcon, $tbName){
    $this->dbcon = $dbcon;
    $this->tb_name = $tbName;
    $this->statement = "show full columns from ".$dbcon->getDBName().'.'.$tbName;
  }

  public function execute(){

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, null, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $result = [];

    /* Prepare statement */
    if($r = $this->dbcon->getDBConn()->query($this->statement)){
      while($row = $r->fetch_assoc()){
        $result[] = $row;
      }
      /* Fetch result to array * /
      if(method_exists($stmt,'get_result')){

        $r = $stmt->get_result();

        if($r->num_rows > 0){
          while($row = $r->fetch_assoc()){
            $result[] = $row;
          }
        }
      }else{

        $stmt->store_result();
        //custom class :D bind to Statement Result mambo jambo!
        $sr = new Statement_Result($stmt);

        if($stmt->num_rows > 0){

          $result = [];

          for($i=0;$i<$prep_stmt->num_rows;$i++){

            $result[] = $sr->Get_Row($prep_stmt);
            $prep_stmt->fetch();

          }

        }
      }*/
    }
    return $result;
  }


}


class mySQL_PDO_GetTableStructureStatement extends mySQLStatement{
  public function __construct($dbcon, $tbName){
    $this->dbcon = $dbcon;
    $this->tb_name = $tbName;
    $this->statement = "show full columns from ".$dbcon->getDBName().'.'.$tbName;
  }

  public function execute(){

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, null, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $result = [];

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */
      $prep_stmt->execute();


      /* Fetch result to array */
      $result = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    return $result;
  }


}

class pgSQL_PDO_GetTableStructureStatement extends mySQLStatement{
  public function __construct($dbcon, $tbName){
    $this->dbcon = $dbcon;
    $this->tb_name = $tbName;
    $this->statement = "select column_name as \"Field\", data_type as \"Type\", collation_name as \"Collation\", is_nullable as \"Null\", '' as \"Key\", column_default as \"Default\", '' as \"Extra\", '' as \"Privileges\", column_name as \"Comment\" from INFORMATION_SCHEMA.COLUMNS where table_name = ? order by ordinal_position"; //.$dbcon->getDBName().'.'.$tbName;
  }

  public function execute(){

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, null, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $result = [];

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */
      $prep_stmt->execute([$this->tb_name]);

      /* Fetch result to array */
      $result = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);

      $get_key_stmt = "SELECT c.column_name as \"KeyField\" FROM information_schema.table_constraints tc JOIN information_schema.constraint_column_usage AS ccu USING (constraint_schema, constraint_name) JOIN information_schema.columns AS c ON c.table_schema = tc.constraint_schema AND tc.table_name = c.table_name AND ccu.column_name = c.column_name where constraint_type = 'PRIMARY KEY' and tc.table_name = ?";

      $prep_stmt = $this->dbcon->getDBConn()->prepare($get_key_stmt);
      if($prep_stmt === false) {
        throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
      }
      else{
        /* Execute statement */
        $prep_stmt->execute([$this->tb_name]);

        /* Fetch result to array */
        $keys = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);

        /* Identify table key fields in the return array*/
        for($k=0;$k<sizeOf($keys);$k++){
          for($i=0;$i<sizeOf($result);$i++){
            if($result[$i]['Field'] == $keys[$k]['KeyField']){
              $result[$i]['Key'] = 'PRI';
              break;
            }
          }
        }
      }

    }

    return $result;
  }


}


class mySQLSelectStatement extends mySQLStatement{

  const ALL_RECORDS = "ALL";
  const FIRST_ONLY  = "FIRST";
  protected $count_statement;

  public function __construct($dbcon, $tb_name = null, $selectionElements = null, $whereConditionElements = null, $selectionOrderElements = null, $max_recs_select = 100){

    $this->statement = "";
    $this->count_statement = "";
    $this->columns = "";
    $this->tb_name = "";
    $this->order = "";
    $this->max_recs_select = $max_recs_select;

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
    $where = "";
    $order = "";

    $this->statement = "SELECT " . $this->columns . " FROM " . ($this->dbcon->prefixDB()?$this->dbcon->getDBName().".":"") . $this->tb_name;
    $this->count_statement = "SELECT count(*) as records_count FROM " . ($this->dbcon->prefixDB()?$this->dbcon->getDBName().".":"") . $this->tb_name;

    if(isset($this->WHERECond) and trim($this->WHERECond->getWHERE())!== ""){
      $where = " WHERE " . $this->WHERECond->getWHERE();
    }

    if(trim($this->order)!==""){
      $order = " ORDER BY ".$this->order;
    }
    $this->statement .= $where . $order;
    $this->count_statement .= $where;

  }

  public function addPagination($pagination){
    $this->preparePagination($pagination);
    $offset = $this->pagination_config['offset'];
    $per_page = $this->pagination_config['per_page'];
    if($per_page > $this->getMaxRecsSelect()){
      throw new Exception('Range requested('.$per_page.') is larger than max records select per page parameter('.$this->getMaxRecsSelect().')!');
    }
    $this->statement .= '  LIMIT ' . $offset .','. $per_page;

  }

  public function execute($return_mode=self::ALL_RECORDS, $pagination=[]){

    $result = array();
    $bind_types = "";
    $parameters = [];

    if($this->WHERECond!=null){
      $bind_types = $this->WHERECond->getBindTypes();
      $parameters = $this->WHERECond->getParameters();
    }

    /* Prepare statement */
    $prep_count_stmt = null;
    if($return_mode == self::ALL_RECORDS ){
      $this->addPagination($pagination);
      $prep_count_stmt = $this->dbcon->getDBConn()->prepare($this->count_statement);
    }

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, [$parameters, $pagination], "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->error.__LINE__ );
    }
    else{
      if(trim($bind_types)!==""){
        $bind_parms = [];
        $bind_parms[] = & $bind_types;
        for($i = 0; $i < count($parameters); $i++){
          $bind_parms[] = & $parameters[$i];
        }
        call_user_func_array(array($prep_stmt, 'bind_param'), $bind_parms);
        if($prep_count_stmt !=null){
          call_user_func_array(array($prep_count_stmt, 'bind_param'), $bind_parms);
        }
      }

      /* Execute statement */
      $prep_stmt->execute() or die($this->dbcon->getDBConn()->error.__LINE__);

      /* Fetch result to array */
      if(method_exists($prep_stmt,'get_result')){
        $r = $prep_stmt->get_result();

        if($r->num_rows > 0){
          if($return_mode == self::ALL_RECORDS ){
            $prep_count_stmt->execute() or die($this->dbcon->getDBConn()->error.__LINE__);
            $c = $prep_count_stmt->get_result();
            $rec = [];
            while($row = $r->fetch_assoc()){
              $rec[] = $row;
            }
            $countr = $c->fetch_assoc();
            $result['records_count'] = $countr['records_count'];
            $result['range_low'] = $this->pagination_config['offset'];
            $result['range_high'] = ( $this->pagination_config['offset'] + sizeOf($rec) ) - 1;
            $result['range_size'] = sizeOf($rec);
            $result['max_size'] = $this->getMaxRecsSelect(); //$this->pagination_config['per_page'];
            $result['data'] = $rec;
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
            $rec = [];

            for($i=0;$i<$prep_stmt->num_rows;$i++){

              $rec[] = $sr->Get_Row($prep_stmt);
              $prep_stmt->fetch();

            }
            $prep_stmt->close();

            $prep_count_stmt->execute() or die($this->dbcon->getDBConn()->error.__LINE__);
            $c = $prep_count_stmt->store_result();
            $sr = new Statement_Result($prep_count_stmt);
            $countr = $sr->Get_Row($prep_count_stmt);
            $prep_count_stmt->fetch();
            $prep_count_stmt->close();
            $result['records_count'] = $countr['records_count'];
            $result['range_low'] = $this->pagination_config['offset'];
            $result['range_high'] = ( $this->pagination_config['offset'] + sizeOf($rec) ) - 1;
            $result['range_size'] = $this->pagination_config['per_page'];
            $result['max_size'] = $this->getMaxRecsSelect(); //$this->pagination_config['per_page'];
            $result['data'] = $rec;

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

class mySQL_PDO_SelectStatement extends mySQLSelectStatement{

  public function execute($return_mode=self::ALL_RECORDS, $pagination=[]){ //Overwriting execute method
    $result = array();
    $bind_types = "";
    $parameters = [];

    if($this->WHERECond!=null){
      $bind_types = $this->WHERECond->getBindTypes();
      $parameters = $this->WHERECond->getParameters();
    }

    /* Prepare statement */
    $prep_count_stmt = null;
    if($return_mode == self::ALL_RECORDS ){
      $this->addPagination($pagination);
      $prep_count_stmt = $this->dbcon->getDBConn()->prepare($this->count_statement);
    }

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, [$parameters, $pagination], "SQL Statement: ".$this->statement ); //Logging SQL Statement

    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */
      if($prep_count_stmt!=null){
        $prep_count_stmt->execute($parameters);
      }
      $prep_stmt->execute($parameters);


      /* Fetch result to array */
      $r = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);
      if($return_mode == self::ALL_RECORDS ){
        $rec = [];

        for($i=0;$i<sizeOf($r);$i++){

          $rec[] = $r[$i];

        }
        $countr = $prep_count_stmt->fetchAll(PDO::FETCH_ASSOC);
        $result['records_count'] = $countr[0]['records_count'];
        $result['range_low'] = $this->pagination_config['offset'];
        $result['range_high'] = ( $this->pagination_config['offset'] + sizeOf($rec) ) - 1;
        $result['range_size'] = sizeOf($r); //$this->pagination_config['per_page'];
        $result['max_size'] = $this->getMaxRecsSelect(); //$this->pagination_config['per_page'];
        $result['data'] = $rec;

      }
      else{

        if(sizeOf($r)>0){
          $result = $r[0];
        }

      }

    }
    return $result;
  }

}



class pgSQL_PDO_SelectStatement extends mySQL_PDO_SelectStatement{

  public function addPagination($pagination){
    $this->preparePagination($pagination);
    $offset = $this->pagination_config['offset'];
    $per_page = $this->pagination_config['per_page'];
    if($per_page > $this->getMaxRecsSelect()){
      throw new Exception('Range requested('.$per_page.') is larger than max records select per page parameter('.$this->getMaxRecsSelect().')!');
    }
    $this->statement .= '  OFFSET ' . $offset .' LIMIT '. $per_page;
  }

  /*public function execute($return_mode=self::ALL_RECORDS, $pagination=[]){ //Overwriting execute method
    $result = array();
    $bind_types = "";
    $parameters = [];

    if($this->WHERECond!=null){
      $bind_types = $this->WHERECond->getBindTypes();
      $parameters = $this->WHERECond->getParameters();
    }

    /* Prepare statement * /
    $prep_count_stmt = null;
    if($return_mode == self::ALL_RECORDS ){
      $this->addPagination($pagination);
      $prep_count_stmt = $this->dbcon->getDBConn()->prepare($this->count_statement);
    }
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement * /
      if($prep_count_stmt!=null){
        $prep_count_stmt->execute($parameters);
      }
      $prep_stmt->execute($parameters);


      /* Fetch result to array * /
      $r = $prep_stmt->fetchAll(PDO::FETCH_ASSOC);
      if($return_mode == self::ALL_RECORDS ){
        $rec = [];

        for($i=0;$i<sizeOf($r);$i++){

          $rec[] = $r[$i];

        }
        $countr = $prep_count_stmt->fetchAll(PDO::FETCH_ASSOC);
        $result['records_count'] = $countr[0]['records_count'];
        $result['range_low'] = $this->pagination_config['offset'];
        $result['range_high'] = ( $this->pagination_config['offset'] + $this->pagination_config['per_page'] ) - 1;
        $result['range_size'] = sizeOf($r); //$this->pagination_config['per_page'];
        $result['data'] = $rec;

      }
      else{

        if(sizeOf($r)>0){
          $result = $r[0];
        }

      }

    }
    return $result;
  }*/

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
    $parameters = $this->bind_parms;

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, $parameters, "SQL Statement: ".$this->statement ); //Logging SQL Statement


    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->error.__LINE__ );
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
      $prep_stmt->execute() or die($this->dbcon->getDBConn()->error.__LINE__);

      /* Get number of lines affected */
      $result = $prep_stmt->affected_rows;

    }
    return $result;
  }

}

class mySQL_PDO_InsertStatement extends mySQLInsertStatement{


  public function execute(){ //Overwriting execute

    $result = 0;
    $parameters = $this->bind_parms;

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, $parameters, "SQL Statement: ".$this->statement ); //Logging SQL Statement


    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */ /* Get number of lines affected */
      $result = $prep_stmt->execute($parameters);


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

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, $parameters, "SQL Statement: ".$this->statement ); //Logging SQL Statement


    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->error.__LINE__ );
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
      $prep_stmt->execute() or die($this->dbcon->getDBConn()->error.__LINE__);

      /* Get number of lines affected */
      $result = $prep_stmt->affected_rows;

    }
    return $result;
  }

}

class mySQL_PDO_UpdateStatement extends mySQLUpdateStatement{

  public function execute(){ //Overwriting execute
    $result = 0;
    $parameters = $this->bind_parms;

    if(sizeof($this->WHERECond->getParameters())>0){
      $b = sizeof($parameters);
      for($i=0;$i<sizeof($this->WHERECond->getParameters());$i++){
        $parameters[$b++] = $this->WHERECond->getParameters()[$i];
      }
    }

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, $parameters, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */ /* Get number of lines affected */
      $result = $prep_stmt->execute($parameters);

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

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, $parameters, "SQL Statement: ".$this->statement ); //Logging SQL Statement


    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->error.__LINE__ );
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
      $prep_stmt->execute() or die($this->dbcon->getDBConn()->error.__LINE__);

      /* Get number of lines affected */
      $result = $prep_stmt->affected_rows;

    }
    return $result;
  }

}


class mySQL_PDO_DeleteStatement extends mySQLDeleteStatement{

  public function execute(){ //Overwriting execute
    $result = 0;
    $parameters = $this->bind_parms;
    if(sizeof($this->WHERECond->getParameters())>0){
      $parameters = $this->WHERECond->getParameters();
    }

    $this->dbcon->getLogger()->log(Logger::LOG_TYPE_Info, $parameters, "SQL Statement: ".$this->statement ); //Logging SQL Statement

    /* Prepare statement */
    $prep_stmt = $this->dbcon->getDBConn()->prepare($this->statement);

    if($prep_stmt === false) {
      throw new Exception('Wrong SQL: ' . $this->statement . ' Error: ' . $this->dbcon->getDBConn()->errorInfo() );
    }
    else{

      /* Execute statement */ /* Get number of lines affected */
      $result = $prep_stmt->execute($parameters);

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
