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
  public function __construct(){
		//parent::__construct();				// Init parent contructor
	}

  /*
	 * Dynmically call the method based on the query string
	 */
	public function processApi(){
    $REST = REST::getInstance();

    $reqInfo = $REST->getRequestInfo(); //Gets the REST path info containing object(s) and parameter(s)

    $request_parts = explode( "/", $reqInfo->getPathInfo() ); //Separating request parts

    $router = new router(); //Initializing router
    $route = $router->getRoute($REST->get_request_method(), $request_parts ); //Obtaining route, containing controller to be instantiated and method to be called, and additional data

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
        $controller->execute($route->getControllerMethod(), $route->getControllerParameters(), $reqInfo->getParameters());

      }
    }else
			$REST->response('',404); // If the method not exist with in this class "Page not found".
	}


}

?>
