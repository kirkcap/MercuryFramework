<?php
namespace com\mercuryfw\helpers;
use com\mercuryfw\helpers\Logger as Logger;
class REST {

	public $_allow = array();
	public $_content_type = "application/json";
	public $_request = array();

	private $_method = "";
	private $_code = 200;

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
		$this->inputs();
	}

	public function get_referer(){
		return $_SERVER['HTTP_REFERER'];
	}

	public function response($data,$status){
		$this->_code = ($status)?$status:200;
		$this->set_headers();
		echo $data;
		exit;
	}
	// For a list of http codes checkout http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
	private function get_status_message(){
		$status = array(
					200 => 'OK',
					201 => 'Created',
					204 => 'No Content',
					404 => 'Not Found',
					400 => 'Error',
					401 => 'Unauthorized',
					406 => 'Not Acceptable');
		return ($status[$this->_code])?$status[$this->_code]:$status[500];
	}

	public function get_request_method(){
		return $_SERVER['REQUEST_METHOD'];
	}

	private function inputs(){
		switch($this->get_request_method()){
			case "POST":
			  $this->_request = empty($_POST)?$this->cleanInputs(file_get_contents("php://input")):$this->cleanInputs($_POST);
				break;
			case "GET":
			case "DELETE":
				$this->_request = empty($_GET)?$this->cleanInputs(file_get_contents("php://input")):$this->cleanInputs($_GET);
				break;
			case "PUT":
				$this->_request = $this->cleanInputs(file_get_contents("php://input")); //$this->_request);
				break;
			case "OPTIONS":
				$this->response('',200);//Responding OK to the processing continue...
				break;
			default:
				$this->response('',406);
				break;
		}
	}

	private function cleanInputs($data){
		$clean_input = array();
		if(is_array($data)){
			foreach($data as $k => $v){
				$clean_input[$k] = $this->cleanInputs($v);
			}
		}else{
			if(get_magic_quotes_gpc()){
				$data = trim(stripslashes($data));
			}
			$data = strip_tags($data);
			$clean_input = trim($data);
		}
		return $clean_input;
	}

  public function getRequestInfo(){

    return new RequestInfo;

  }

  /*
	 *	Encode array into JSON
	*/
	public static function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}


	private function set_headers(){

		header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		header("Content-Type:".$this->_content_type);
		header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
		header("Access-Control-Max-Age: 1000");
    header("Access-Control-Allow-Headers: Content-Type, token, Authorization, X-Requested-With");
    header("Access-Control-Allow-Credentials: true");

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

class RequestInfo{
	private $pathInfo;
	private $parameters = [];

	public function __construct(){
    $this->prepare();
	}

	public function prepare(){
    if(!empty($_SERVER['PATH_INFO'])){
      $this->pathInfo = ltrim(rtrim($_SERVER['PATH_INFO'], "/"),"/");//Removing first and last /
			if(!empty($_SERVER["QUERY_STRING"])){
				$params = explode('&', empty($_SERVER["QUERY_STRING"]));
				$this->processParameters($params);
			}
    }else{
			if(!empty($_SERVER["QUERY_STRING"]) || !empty($_SERVER["REDIRECT_QUERY_STRING"])){
				if(!empty($_SERVER["QUERY_STRING"])){
			    $queryStr    = $_SERVER["QUERY_STRING"];
				}else {
					$queryStr    = $_SERVER["REDIRECT_QUERY_STRING"];
				}
				$queryParts  = explode('&', $queryStr);//Separating first part from the rest...
				if(sizeOf($queryParts)>0){
					$this->pathInfo = explode('=', $queryParts[0])[1];//Expecting <var>=pathInfo
					for($i=0;$i<sizeOf($queryParts)-1;$i++){
					  $queryParts[$i] = $queryParts[$i+1];// Overwriting first position, which contains the pathInfo data, the remaining, are query string parameters...
					}
					unset($queryParts[sizeOf($queryParts)-1]);//Removing last position
					$this->processParameters($queryParts);
				}
			}else{
	      $baseUrl     = preg_replace('/index*.php$/', '', $_SERVER['SCRIPT_NAME']);//Removing script name to get the baseUrl(if script is hosted in subfolder from the root)
	      $scriptName  = basename($_SERVER["SCRIPT_FILENAME"]);//Obtaining the exact name of the index*.php script
	      $this->pathInfo    = substr($_SERVER['REQUEST_URI'], mb_strlen($baseUrl));//Removing the baseUrl from the REQUEST_URI, to get pathInfo
				if(strpos($this->pathInfo, $scriptName)!==false){ //If script name is on the REQUEST_URI, remove it also...
	        $this->pathInfo    = substr($pathInfo, mb_strlen($scriptName));
				}
	      $this->pathInfo = ltrim(rtrim($this->pathInfo, "/"),"/");//Removing first and last /
		  }
    }
	}

	public function processParameters($params){
		$reqParams = [];
		for($i=0;$i<sizeOf($params);$i++){
			$reqParams[] = new RequestParameter($params[$i]);
		}
		$this->setParameters($reqParams);
	}

	public function setPathInfo($data){
		$this->pathInfo = $data;
	}
	public function getPathInfo(){
		return $this->pathInfo;
	}

	public function setParameters($data){
		$this->parameters = $data;
	}
	public function getParameters(){
		return $this->parameters;
	}
}

class RequestParameter{
	private $name;
	private $value;

	public function __construct($param){
		$parts = explode('=', $param);
		$this->setName($parts[0]);
		$this->setValue($parts[1]);
	}

	public function setName($data){
		$this->name = $data;
	}
	public function getName(){
		return $this->name;
	}

	public function setValue($data){
		$this->value = $data;
	}
	public function getValue(){
		return $this->value;
	}

}
?>
