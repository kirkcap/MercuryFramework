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
* @category API
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

  define('__ROOT__', dirname(dirname(__FILE__)));
  require_once __ROOT__."/backend/Rest.inc.php";
  require_once __ROOT__."/backend/api/application/helpers/jwt_helper.php";
  require_once __ROOT__."/backend/api/application/helpers/auth_helper.php";
  require_once __ROOT__."/backend/api/application/helpers/models_helper.php";
  require_once __ROOT__."/backend/api/router/router.php";
  require_once __ROOT__."/backend/api/controllers/ControllerFactory.php";
  require_once __ROOT__."/backend/api/controllers/genericCRUDController.php";
  require_once __ROOT__."/backend/api/controllers/genericAuthController.php";
  require_once __ROOT__."/backend/api/controllers/geoUtilController.php";
  require_once __ROOT__."/backend/api/application/helpers/db_helper.php";


	class API extends REST {

		public function __construct(){
			parent::__construct();				// Init parent contructor
		}

    /*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){

      $pathInfo = $this->getPathInfo(); //Gets the REST path info containing object(s) and parameter(s)

      $request_parts = explode( "/", $pathInfo ); //Separating request parts

      $router = new router(); //Initializing router
      $route = $router->getRoute($this->get_request_method(), $request_parts ); //Obtaining route, containing controller to be instantiated and method to be called, and additional data

      if($route->getController()!=null){
        $continue = true;

        if($route->getCheckToken()){
          $token = Token::validateToken();
          if(!$token->isValid()){
            $continue = false;
            $this->response($this->json($token->getDiagnostic()),401);
          }
        }

        if($continue){

          if($route->getController() == "genericCRUDController" or
             $route->getController() == "genericAuthController" ){

            $controller = ControllerFactory::getController($route->getController(),array($this, $route->getModel()));

          }else{
            if($route->getModel()!=null){
              $controller = ControllerFactory::getController($route->getController(),array($this, $route->getModel()));

            }else{
              $controller = ControllerFactory::getController($route->getController(),array($this));

            }
          }

          $controller->execute($route->getControllerMethod(), $route->getControllerParameters());

        }
      }else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		}

    private function getPathInfo(){
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
		public function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}

	// Initiiate Library

	//$api = new API;
	//$api->processApi();
?>
