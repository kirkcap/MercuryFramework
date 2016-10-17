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
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/


	class REST {

		public $_allow = array();
		public $_content_type = "application/json";
		public $_request = array();

		private $_method = "";
		private $_code = 200;

		public function __construct(){
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

		private function set_headers(){

			header("HTTP/1.1 ".$this->_code." ".$this->get_status_message());
			header("Content-Type:".$this->_content_type);
			header("Access-Control-Allow-Origin: *");
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, PATCH, DELETE");
			header("Access-Control-Max-Age: 1000");
      header("Access-Control-Allow-Headers: Content-Type, token, Authorization, X-Requested-With");
      header("Access-Control-Allow-Credentials: true");

		}
	}
?>
