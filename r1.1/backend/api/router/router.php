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
* @category Router
* @package  com\mercuryfw\routers
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

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
    $routesModel = new configModel("routes");
    $this->routes = $routesModel->listAll(); //json_decode(file_get_contents(__ROOT__."/backend/config/routes.json"), true); //Getting routes config data
    $cfgModel = new configModel("admin_cfg");
    $r = $cfgModel->findByKey("admin_frontend_allowed");
    if(sizeOf($r)>0 && $r["admin_frontend_allowed"]){
      $this->routes["admin_cfg"] = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"admin_cfg\"}", true);
      $this->routes["auth_cfg"]  = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"auth_cfg\"}", true);
      $this->routes["databases"] = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"databases\"}", true);
      $this->routes["models"]    = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"models\"}", true);
      $this->routes["routes"]    = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"routes\"}", true);
      $this->routes["dbMetadata"]  = json_decode("{\"controller\" : \"dbMetadataController\", \"method\" : \"getDBMetadata\", \"checkToken\" : false}", true);
      $this->routes["dbMetadata.tbMetadata"]  = json_decode("{\"controller\" : \"dbMetadataController\", \"method\" : \"getTBMetadata\", \"checkToken\" : false}", true);
      //$this->routes["config_files"]  = json_decode("{\"controller\" : \"configurationController\", \"method\" : \"CRUD\", \"checkToken\" : false, \"model\" : \"config_files\"}", true);
    }
    //{  "object"              : {"controller" : "<controller>","method" : "<method>", "checkToken" : true/false, ["model" : "<model>"]}} where method = CRUD|Method Name, model=Model Name
  }

  public function getAllowedMethods($request_parts){
    $method = "";
    $srd = new serviceRequestedData($request_parts);
    if(array_key_exists($srd->getRouteName(), $this->routes)){
      switch($this->routes[$srd->getRouteName()]["method"]){
        case "CRUD":
          $method = "GET,POST,PUT,DELETE,OPTIONS";
          break;
        case "index":
          $method = "GET,OPTIONS";
          break;
        case "show":
          $method = "GET,OPTIONS";
          break;
        case "create":
          $method = "POST,OPTIONS";
          break;
        case "update":
          $method = "PUT,OPTIONS";
          break;
        case "destroy":
          $method = "DELETE,OPTIONS";
          break;
        case "login":
          $method = "POST,OPTIONS";
          break;
        default:
          $method = $this->routes[$srd->getRouteName()]["http_method"].",OPTIONS";
          break;
      }
      return $method;
    }else{
      return "NOROUTE!";
    }
  }

  public function getRoute( $httpMethod, $request_parts){

    $route = new route();

    $sep        = "";
    $counter    = 0;
    $objCounter = 0;
    $checkToken = false;

    $srd = new serviceRequestedData($request_parts);


    if(array_key_exists($srd->getRouteName(), $this->routes)){
      $route->setController($this->routes[$srd->getRouteName()]["controller"]);
      $route->setRouteMethod($this->routes[$srd->getRouteName()]["method"]);
      $route->setCheckToken($this->routes[$srd->getRouteName()]["checkToken"]);

      if(array_key_exists("model",$this->routes[$srd->getRouteName()])){
        $route->setModel($this->routes[$srd->getRouteName()]["model"]);
      }elseif($this->routes[$srd->getRouteName()]['controller']=='dbMetadataController'){ //If it´s a call to dbMetadataController, the first param is the DBConfig name, the 'model'
        $route->setModel($srd->getRouteParams()[0]); //$parms[0]);
      }



      if($route->getRouteMethod() == "CRUD"){
        if($httpMethod == "GET"){//Get object data
           //if(sizeof($objs) > sizeof($parms)){//Request all elements
           if(sizeof($srd->getRouteParts()) > sizeof($srd->getRouteParams())){//Request all elements
             $route->setControllerMethod("index") ;
             //if(sizeof($parms)>0){
             if(sizeof($srd->getRouteParams())>0){
               $route->setControllerParameters($srd->getRouteParams()); //$parms);
             }

           }else{
             $route->setControllerMethod("show");
             $route->setControllerParameters($srd->getRouteParams()); //$parms);
           }

        }elseif($httpMethod == "POST"){//Create object data
           $route->setControllerMethod("create");

           //if(sizeof($parms)>0){
           if(sizeof($srd->getRouteParams())>0){
             $route->setControllerParameters($srd->getRouteParams()); //$parms);
           }

        }elseif($httpMethod == "PUT"){//Update object data
           $route->setControllerMethod("update");
           $route->setControllerParameters($srd->getRouteParams()); //$parms);

        }elseif($httpMethod == "DELETE"){//Delete object data
           $route->setControllerMethod("destroy");
           $route->setControllerParameters($srd->getRouteParams()); //$parms);

        }
      }else{
        $route->setControllerMethod($route->getRouteMethod());
        //if(sizeof($parms)>0){
        if(sizeof($srd->getRouteParams())>0){
          $route->setControllerParameters($srd->getRouteParams()); //$parms);
        }
      }

      return $route;

    }else{
      //REST::getInstance()->response(["error" => "No route found for specified address!"],200);
      throw new Exception("No route found for specified address!");
    }
  }
}


class serviceRequestedData{

  private $routeName;
  private $routeParts;
  private $routeParams;

  public function __construct($request_parts){
    $sep         = "";
    $counter     = 0;
    $objCounter  = 0;
    $routeParts  = array();
    $routeParams = array();
    $routeName   = "";

    foreach($request_parts as $part){
      if($counter++ == 0){
        $routeParts[$objCounter] = $part;
        $routeName .= $sep . $part;
        $sep = '.';
      }else{
        $routeParams[$objCounter++] = $part;
        $counter = 0;
      }
    }
    $this->setRouteName($routeName);
    $this->setRouteParts($routeParts);
    $this->setRouteParams($routeParams);
  }

  public function setRouteName($name){
    $this->routeName = $name;
  }
  public function getRouteName(){
    return $this->routeName;
  }

  public function setRouteParts($parts){
    $this->routeParts = $parts;
  }
  public function getRouteParts(){
    return $this->routeParts;
  }

  public function setRouteParams($parms){
    $this->routeParams = $parms;
  }
  public function getRouteParams(){
    return $this->routeParams;
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
