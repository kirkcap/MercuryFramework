<?php
namespace com\mercuryfw\routers;
require_once(__ROOT__."/backend/api/model/configModel.php");
use com\mercuryfw\models\configModel as configModel;
use com\mercuryfw\helpers\REST as REST;
/**
 * Router implementation
 *
 *
 * PHP version 5
 *
 * @category Router
 * @package  router
 * @author   Wilson Rodrigo dos Santos(wilson.rsantos@gmail.com)
 */
class router{

  protected $routes;

  public function __construct(){
    $this->routes = json_decode(file_get_contents(__ROOT__."/backend/config/routes.json"), true); //Getting routes config data
    $cfgModel = new configModel("admin_cfg");
    $r = $cfgModel->findByKey("admin_frontend_allowed");
    if(sizeOf($r)>0 && $r["admin_frontend_allowed"]){
      $this->routes["admin_cfg"] = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"admin_cfg\"}", true);
      $this->routes["auth_cfg"]  = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"auth_cfg\"}", true);
      $this->routes["databases"] = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"databases\"}", true);
      $this->routes["models"]    = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"models\"}", true);
      $this->routes["routes"]    = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"routes\"}", true);
      //$this->routes["config_files"]  = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"config_files\"}", true);
    }
    $this->routes["dbMetadata"]  = json_decode("{\"controller\" : \"dbMetadataController\", \"method\" : \"getDBMetadata\", \"checkToken\" : false}", true);
    $this->routes["dbMetadata.tbMetadata"]  = json_decode("{\"controller\" : \"dbMetadataController\", \"method\" : \"getTBMetadata\", \"checkToken\" : false}", true);
    //{  "object"              : {"controller" : "<controller>","method" : "<method>", "checkToken" : true/false, ["model" : "<model>"]}} where method = CRUD|Method Name, model=Model Name
  }

  public function getRoute( $httpMethod, $request_parts){

    $route = new route();

    $sep        = "";
    $counter    = 0;
    $objCounter = 0;
    $checkToken = false;
    $objs       = array();
    $parms      = array();
    $object     = "";
    $parameters = array();

    foreach($request_parts as $part){
      if($counter++ == 0){
        $objs[$objCounter] = $part;
        $object = $object . $sep . $part;
        $sep = '.';
      }else{
        $parms[$objCounter++] = $part;
        $counter = 0;
      }
    }

    $result = array();

    foreach($this->routes as $key => $value){

      if($key == $object){
        $route->setController($value["controller"]);
        $route->setRouteMethod($value["method"]);
        $route->setCheckToken($value["checkToken"]);

        if(array_key_exists("model",$value)){
          $route->setModel($value["model"]);
        }elseif($value['controller']=='dbMetadataController'){ //If it´s a call to dbMetadataController, the first param is the DBConfig name, the 'model'
          $route->setModel($parms[0]);
        }
        break;
      }
    }

    if($route->getController() != null){//If a controller was found...

      if($route->getRouteMethod() == "CRUD"){
        if($httpMethod == "GET"){//Get object data
           if(sizeof($objs) > sizeof($parms)){//Request all elements
             $route->setControllerMethod("index") ;
             if(sizeof($parms)>0){
               $route->setControllerParameters($parms);
             }

           }else{
             $route->setControllerMethod("show");
             $route->setControllerParameters($parms);
           }

        }elseif($httpMethod == "POST"){//Create object data
           $route->setControllerMethod("create");

           if(sizeof($parms)>0){
             $route->setControllerParameters($parms);
           }

        }elseif($httpMethod == "PUT"){//Update object data
           $route->setControllerMethod("update");
           $route->setControllerParameters($parms);

        }elseif($httpMethod == "DELETE"){//Delete object data
           $route->setControllerMethod("destroy");
           $route->setControllerParameters($parms);

        }
      }else{
        $route->setControllerMethod($route->getRouteMethod());
        if(sizeof($parms)>0){
          $route->setControllerParameters($parms);
        }
      }

      return $route;
    }else{
      REST::getInstance()->response("No route found for specified address!",200);
    }
  }
}

class route{
  private $controller;
  private $controller_method;
  private $controller_parameters;
  private $route_method;
  private $model;
  private $check_token;

  public function __construct(){
    $this->controller = null;
    $this->controller_method = null;
    $this->controller_parameters = null;
    $this->route_method = null;
    $this->model = null;
    $this->check_token = false;
  }

  public function setController($controller){
    $this->controller = $controller;
  }
  public function getController(){
    return $this->controller;
  }

  public function setControllerMethod($method){
    $this->controller_method = $method;
  }
  public function getControllerMethod(){
    return $this->controller_method;
  }

  public function setControllerParameters($parameters){
    $this->controller_parameters = $parameters;
  }
  public function getControllerParameters(){
    return $this->controller_parameters;
  }

  public function setRouteMethod($method){
    $this->route_method = $method;
  }
  public function getRouteMethod(){
    return $this->route_method;
  }

  public function setModel($model){
    $this->model = $model;
  }
  public function getModel(){
    return $this->model;
  }

  public function setCheckToken($checkToken){
    $this->check_token = $checkToken;
  }
  public function getCheckToken(){
    return $this->check_token;
  }

}

?>
