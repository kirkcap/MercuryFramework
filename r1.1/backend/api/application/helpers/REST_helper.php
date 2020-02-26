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
* @category REST API Base
* @package  com\mercuryfw\helpers
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

namespace com\mercuryfw\helpers;
use com\mercuryfw\helpers\Logger as Logger;
use com\mercuryfw\models\configModel as configModel;
use com\mercuryfw\routers\router as router;

class REST {

	public  $_allow = array();
	public  $_content_type = "application/json";
	private $_request = array();
	private $_router;
	private $_request_parts = null;
	private $_route;

	private $_method = "";
	private $_code = 200;
	private $_allowed_methods = "";
	private $_http_method_allowed = false;

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

		$this->setRouter(new router());
		$this->setAllowedMethods($this->getRouter()->getAllowedMethods($this->getRequestParts()));

		$this->inputs();

	}

  private function getRequestParts(){
		if($this->_request_parts==null){
			$reqInfo = $this->getRequestInfo(); //Gets the REST path info containing object(s) and parameter(s)
			$this->_request_parts = explode( "/", $reqInfo->getPathInfo() ); //Separating request parts
		}
		return $this->_request_parts;
	}

	private function setRouter($router){
		$this->_router = $router;
	}
	public function getRouter(){
		return $this->_router;
	}

	private function setRoute($route){
		$this->_route = $route;
	}
	public function getRoute(){
		return $this->_route;
	}

	private function setAllowedMethods($methods){
		$this->_allowed_methods = $methods;
	}
	public function getAllowedMethods(){
		return $this->_allowed_methods;
	}

	private function setHTTPMethodIsAllowed($allowed){
		$this->_http_method_allowed = $allowed;
	}
	public function HTTPMethodIsAllowed(){
		return $this->_http_method_allowed;
	}

	public function get_referer(){
		return $_SERVER['HTTP_REFERER'];
	}

	public function response($data,$status,$additional_headers=[]){
		$this->_code = ($status)?$status:200;
		$this->set_headers($additional_headers);
		echo $data;
		exit;
	}


	// For a list of http codes checkout http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
	private function get_status_message(){
		$status = array(
					200 => 'OK',
					201 => 'Created',
					204 => 'No Content',
					206 => 'Partial Content',
					404 => 'Not Found',
					400 => 'Error',
					401 => 'Unauthorized',
					403 => 'Forbidden',
					406 => 'Not Acceptable');
		return ($status[$this->_code])?$status[$this->_code]:$status[500];
	}

	public function get_request_method(){
		return $_SERVER['REQUEST_METHOD'];
	}

	private function inputs(){
		if($this->getAllowedMethods()!="NOROUTE!" && strpos($this->getAllowedMethods(), $this->get_request_method())!==false){
			$this->setHTTPMethodIsAllowed(true);
			$this->setRoute($this->getRouter()->getRoute($this->get_request_method(), $this->getRequestParts()));
			switch($this->get_request_method()){
				case "POST":
				  
				  $this->_request = empty($_FILES)?(empty($_POST)?$this->cleanInputs(file_get_contents("php://input")):$this->cleanInputs($_POST)):$this->cleanInputs($_FILES);
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
		}else{
			$this->setHTTPMethodIsAllowed(false);
		}
	}

	public function getRequestData(){
		return $this->_request;
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
	
	public static function imgResponse($file_out){
		if (file_exists($file_out)) {

			//Set the content-type header as appropriate
			$image_info = getimagesize($file_out);
			switch ($image_info[2]) {
				case IMAGETYPE_JPEG:
					header("Content-Type: image/jpeg");
					break;
				case IMAGETYPE_GIF:
					header("Content-Type: image/gif");
					break;
				case IMAGETYPE_PNG:
					header("Content-Type: image/png");
					break;
			   default:
					header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
					break;
			}

			// Set the content-length header
			header('Content-Length: ' . filesize($file_out));

			// Write the image bytes to the client
			readfile($file_out);

		}
		else { // Image file not found

			header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");

		}
	}

	public static function imgResponseNew($file_out, $width = 0, $quality = 100){
		if (file_exists($file_out)) {
			$image_info = getimagesize($file_out);
			switch ($image_info[2]) {
				case IMAGETYPE_JPEG:
					header("Content-Type: image/jpeg");
					break;
				case IMAGETYPE_GIF:
					header("Content-Type: image/gif");
					break;
				case IMAGETYPE_PNG:
					header("Content-Type: image/png");
					break;
			default:
					header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
					break;
			}
			if($width == 0){
				//Set the content-type header as appropriate
				

				// Set the content-length header
				header('Content-Length: ' . filesize($file_out));

				// Write the image bytes to the client
				readfile($file_out);

			}else{

				REST::compress_image( $file_out, $width, 0, $quality);

			}

		}
		else { // Image file not found

			header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");

		}
	}

	private static function compress_image($source_file, $nwidth, $nheight, $quality) {
		//Return an array consisting of image type, height, widh and mime type.
		$image_info = getimagesize($source_file);
		if($nwidth > 0){
			$nheight = $image_info[1] * $nwidth / $image_info[0];
		}elseif($nheight > 0){
			$nwidth  = $image_info[1] * $nheight / $image_info[0];
		}
		if(!($nwidth > 0)) $nwidth = $image_info[0];
			
		if(!($nheight > 0)) $nheight = $image_info[1];
			
		
		if(!empty($image_info)) {
			switch($image_info['mime']) {
				case 'image/jpeg' :
					if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
					// Create a new image from the file or the url.
					$image = imagecreatefromjpeg($source_file);
					$thumb = imagecreatetruecolor($nwidth, $nheight);
					//Resize the $thumb image
					imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
					//Output image to the browser or file.
					$res = imagejpeg($thumb, null, $quality); 
					imagedestroy($thumb);
					return $res;
					
				break;
				
				case 'image/png' :
					if($quality == '' || $quality < 0 || $quality > 9) $quality = 6; //Default quality
					// Create a new image from the file or the url.
					$image = imagecreatefrompng($source_file);
					$thumb = imagecreatetruecolor($nwidth, $nheight);
					//Resize the $thumb image
					imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
					// Output image to the browser or file.
					$res = imagepng($thumb, null, $quality);
					imagedestroy($thumb);
					return $res;
				break;
				
				case 'image/gif' :
					if($quality == '' || $quality < 0 || $quality > 100) $quality = 75; //Default quality
					// Create a new image from the file or the url.
					$image = imagecreatefromgif($source_file);
					$thumb = imagecreatetruecolor($nwidth, $nheight);
					//Resize the $thumb image
					imagecopyresized($thumb, $image, 0, 0, 0, 0, $nwidth, $nheight, $image_info[0], $image_info[1]);
					// Output image to the browser or file.
					$res = imagegif($thumb, null, $quality); //$success = true;
					imagedestroy($thumb);
					return $res;
				break;
				
				default:
					echo "<h4>File type not supported!</h4>";
				break;
			}
		}
	}

	public static function vidResponse($file_out){
		if (file_exists($file_out)) {
			/*
			//Set the content-type header as appropriate
			$mime_type = REST::get_mime_type($file_out);
			header("Content-Type: " . $mime_type);
			
			// Set the content-length header
			header('Content-Length: ' . filesize($file_out));

			// Write the image bytes to the client
			readfile($file_out);*/

			$mime_type = REST::get_mime_type($file_out);
			$fp = fopen($file_out, "rb");
			$size = filesize($file_out);
			$length = $size;
			$start = 0;
			$end = $size - 1;
			header('Content-type: ' . $mime_type);
			header("Accept-Ranges: 0-$length");
			if (isset($_SERVER['HTTP_RANGE'])) {
				$c_start = $start;
				$c_end = $end;
				list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);

				if (strpos($range, ',') !== false) {
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$size");
					exit;
				}

				if ($range == '-') {
					$c_start = $size - substr($range, 1);
				} else {
					$range = explode('-', $range);
					$c_start = $range[0];
					$c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
				}

				$c_end = ($c_end > $end) ? $end : $c_end;

				if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$size");
					exit;
				}

				$start = $c_start;
				$end = $c_end;
				$length = $end - $start + 1;
				fseek($fp, $start);
				header('HTTP/1.1 206 Partial Content');
			}

			header("Content-Range: bytes $start-$end/$size");
			header("Content-Length: ".$length);

			$buffer = 1024 * 8;

			while(!feof($fp) && ($p = ftell($fp)) <= $end) {
				if ($p + $buffer > $end) {
					$buffer = $end - $p + 1;
				}
				set_time_limit(0);
				echo fread($fp, $buffer);
				flush();
			}

			fclose($fp);
			exit;

		}
		else { // Image file not found

			header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");

		}
	}

	public static function get_mime_type($filename) {
		$idx = explode( '.', $filename );
		$count_explode = count($idx);
		$idx = strtolower($idx[$count_explode-1]);
	
		$mimet = array( 
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',
	
			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',
	
			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',
	
			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'mp4' => 'video/mp4',
	
			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
	
			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			'docx' => 'application/msword',
			'xlsx' => 'application/vnd.ms-excel',
			'pptx' => 'application/vnd.ms-powerpoint',
	
	
			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		if (isset( $mimet[$idx] )) {
			return $mimet[$idx];
		} else {
			return 'application/octet-stream';
		}
	}


	private function set_headers($additional_headers=[]){
		$cfgModel = new configModel("admin_cfg");
		$r = $cfgModel->findByKey("cors_allow_origin");

		header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
		header("Content-Type:".$this->_content_type);

		if(sizeOf($r)>0 && $r["cors_allow_origin"]){
			header("Access-Control-Allow-Origin: " . $r["cors_allow_origin"]);
		}else{
			header("Access-Control-Allow-Origin: localhost");
		}

		header("Access-Control-Allow-Methods: ". $this->getAllowedMethods() ); //GET, POST, OPTIONS, PUT, DELETE");
		header("Access-Control-Max-Age: 1000");
		header("Access-Control-Allow-Headers: Content-Type, token, MTokenLA, Authorization, X-Requested-With, enctype, Accept");
		header("Access-Control-Allow-Credentials: true");
		if(sizeOf($additional_headers)>0){
			for($i=0;$i<sizeOf($additional_headers);$i++){
				header($additional_headers[$i]['name'].":".$additional_headers[$i]['value']);
			}
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
					$params = explode('&', $_SERVER["QUERY_STRING"]);

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
						$this->pathInfo = substr($pathInfo, mb_strlen($scriptName));
					}
					$this->pathInfo = ltrim(rtrim($this->pathInfo, "/"),"/");//Removing first and last /
			    }
		}
	}

	public function processParameters($params){
		$reqParams = [];
		$pagParams = [];
		$srtParams = [];
		for($i=0;$i<sizeOf($params);$i++){
			$reqParam = new RequestParameter($params[$i]);
			if($reqParam->isFilterCriteria()){
			  $reqParams[] = $reqParam;
			}elseif($reqParam->isPagination()){
				$pagParams[] = $reqParam;
			}else{
				$srtParams[] = $reqParam;
			}
		}
		$this->setParameters(['filter_criteria' => $reqParams, 'pagination' => $pagParams, 'sort' => $srtParams ]);
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

	public function setPagination($data){
		$this->pagination = $data;
	}
	public function getPagination(){
		return $this->pagination;
	}
}

class RequestParameter{
	private $name;
	private $value_low;
	private $value_high;
	private $type;

	public function __construct($param){
		$parts = explode('=', $param);
		$this->setName($parts[0]);
		if(strtolower($parts[0])=='_range'){
			$range = explode('-', $parts[1]);
			$this->setValueLow($range[0]);
			$this->setValueHigh($range[1]);
			$this->setType('pagination');
		}elseif(strtolower($parts[0])=='_page' || strtolower($parts[0])=='_per_page'){
			$this->setValue($parts[1]);
			$this->setType('pagination');
		}elseif(strtolower($parts[0])=='_sort' || strtolower($parts[0])=='_desc'){
			$this->setValue($parts[1]);
			$this->setType('sort');
		}else{
			$this->setValue($parts[1]);
			$this->setType('filter_criteria');
		}
	}

	public function setName($data){
		$this->name = $data;
	}
	public function getName(){
		return $this->name;
	}

	public function setValue($data){
		$this->value_low = $data;
	}
	public function getValue(){
		return $this->value_low;
	}

	public function setValueLow($data){
		$this->value_low = $data;
	}
	public function getValueLow(){
		return $this->value_low;
	}

	public function setValueHigh($data){
		$this->value_high = $data;
	}
	public function getValueHigh(){
		return $this->value_high;
	}

	public function setType($data){
		$this->type = $data;
	}
	public function getType(){
		return $this->type;
	}

	public function isFilterCriteria(){
		return $this->type == 'filter_criteria';
	}

	public function isPagination(){
		return $this->type == 'pagination';
	}

	public function isSortParameter(){
		return $this->type == 'sort';
	}


}
?>
