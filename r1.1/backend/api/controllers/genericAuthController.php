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
* @category Controllers
* @package  com\mercuryfw\controllers
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

namespace com\mercuryfw\controllers;
require_once("genericCRUDController.php");
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\helpers\Token as Token;
use com\mercuryfw\models\genericModel as genericModel;

class genericAuthController extends genericCRUDController{

    private $id_field;
    private $login_field;
    private $modelObj;
    private $pwd_field;

    public function __construct($ModelName){

      $this->API = REST::getInstance();
      $this->modelName     = $ModelName;
      $this->modelObj      = new genericModel($this->modelName);
      $this->id_field      = $this->modelObj->getTableKey()[0];
      $this->login_field   = $this->modelObj->getLoginField(); //ModelData["login_field"];
      $this->pwd_field     = $this->modelObj->getPwdField(); //ModelData["pwd_field"];

    }

    public function login(){
      if($this->API->get_request_method() != "POST"){
        $this->API->response('Invalid method:' . $this->API->get_request_method() ,406);
      }


      $credentials = json_decode($this->API->getRequestData(),true); //json_decode(file_get_contents("php://input"),true);

      $login = "";
      $password = "";

      if($credentials){
        $login    = $credentials[$this->login_field];
        $password = $credentials[$this->pwd_field];
      }

      if(!empty($login) and !empty($password)){

        $critfields = [$this->login_field, $this->pwd_field];
        $critvalues = [$login, md5($password)];
        $r = $this->modelObj->find($critfields, $critvalues);
        if($this->modelObj->exceptionOcurred()){
          $this->API->response($this->API->json($modelObj->getErrorData()->getFrontEndResponse()),200);
        }else{

          if(sizeof($r) > 0){
            $token = Token::getInstance();

            $enc_token = $token->build($r[$this->id_field]); //login_field]);

            $success = array( 'response' => "ok", 'user_data' => $r, 'auth_token' => $enc_token); // "{\"response\":\"ok\",\"auth_token\":\"" . $this->buildToken( $email ) . "\"}";

            $this->API->response($this->API->json($success), 200);

          }
          else{
            $error = array('status' => "Failed", "msg" => "Invalid Credentials" );
            $this->API->response($this->API->json($error), 400);
          }
        }


      }
      else{
        $error = array('status' => "Failed", "msg" => "Credentials must be filled: " . $login . " - " . $password );
        $this->API->response($this->API->json($error), 400);
      }

    }


    public function index($parm){

      $this->API->response('',204);	//Listing of users is not allowed

		}

    public function destroy($parm){

      $this->API->response('',204);	//Deletion of users is not allowed


    }

}

?>
