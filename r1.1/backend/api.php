<?php
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
