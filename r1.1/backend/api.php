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
* @category REST API Builder
* @package  com\mercuryfw\api
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*API Class - This is the main class of the project, which receives the requests redirected
*by the index_mc.php page, and start routers and controllers to deal with them.
*/

namespace com\mercuryfw\api;
define('__ROOT__', dirname(dirname(__FILE__)));
require_once __ROOT__."/backend/api/application/helpers/debug.php";
require_once __ROOT__."/backend/api/application/helpers/utils_helper.php";
require_once __ROOT__."/backend/api/application/helpers/REST_helper.php";
require_once __ROOT__."/backend/api/application/helpers/logger_helper.php";
require_once __ROOT__."/backend/config/loader.php";
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\routers\router as router;
use com\mercuryfw\controllers\ControllerFactory as ControllerFactory;
use com\mercuryfw\helpers\Token as Token;
use com\mercuryfw\helpers\Logger as Logger;

class API {
	
	private $logger;
	
	public function __construct(){
		//parent::__construct();				// Init parent contructor
		$this->logger = Logger::getInstance();
	}

  /*
	 * Dynmically call the method based on the query string
	 */
	public function processApi(){
		$REST = REST::getInstance();
		
		//$this->logger->log(Logger::LOG_TYPE_Info, null, "Chegou aqui 1!". var_dump(file_get_contents("php://input")) . var_dump($_POST) . var_dump($_FILES) ); 

		if($REST->HTTPMethodIsAllowed()){

		  $route = $REST->getRoute();

		  if($route->getController()!=null){
			$continue = true;

			if($route->getCheckToken()){
			  $token = Token::validateToken();
			  if(!$token->isValid()){
				$continue = false;
				$REST->response($REST->json($token->getDiagnostic()),401);
			  }
			}

			if($continue){
			  if($route->getController() == "genericCRUDController" or
				 $route->getController() == "genericAuthController" ){

				$controller = ControllerFactory::getController($route->getController(),array($route->getModel()));

			  }else{
				if($route->getModel()!=null){
				  $controller = ControllerFactory::getController($route->getController(),array($route->getModel()));

				}else{
				  $controller = ControllerFactory::getController($route->getController(),array());

				}
			  }
			  $controller->execute($route->getControllerMethod(), $route->getControllerParameters(), $REST->getRequestInfo()->getParameters());

			}
		  }else
				$REST->response('',404); // If the method not exist with in this class "Page not found".
		}else
		  $REST->response('',406); //Not acceptable
	}


}

?>
