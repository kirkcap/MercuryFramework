<?php
namespace com\mercuryfw\helpers;

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

  public function getPathInfo(){
    if(!empty($_SERVER['PATH_INFO'])){
      $pathInfo = ltrim(rtrim($_SERVER['PATH_INFO'], "/"),"/");//Removing first and last /
    }else{
      $baseUrl     = preg_replace('/index*.php$/', '', $_SERVER['SCRIPT_NAME']);//Removing script name to get the baseUrl(if script is hosted in subfolder from the root)
      $scriptName  = basename($_SERVER["SCRIPT_FILENAME"]);//Obtaining the exact name of the index*.php script
      $pathInfo    = substr($_SERVER['REQUEST_URI'], mb_strlen($baseUrl));//Removing the baseUrl from the REQUEST_URI, to get pathInfo
      if(strpos($pathInfo, $scriptName)!==false){ //If script name is on the REQUEST_URI, remove it also...
        $pathInfo    = substr($pathInfo, mb_strlen($scriptName));
      }
      $pathInfo = ltrim(rtrim($pathInfo, "/"),"/");//Removing first and last /
    }
    return $pathInfo;
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
?>
