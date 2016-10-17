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
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

//require_once(__ROOT__."/backend/api/application/helpers/sql_helper.php");
require_once(__ROOT__."/backend/api/model/genericModel.php");

class genericCRUDController{
/*
* Makers API
*/
    protected $API;
    protected $modelName;


    public function __construct($REST_API, $ModelName){

      $this->API = $REST_API;
      $this->modelName = $ModelName;

    }

    public function execute($method, $parameter){
      //$token = Token::getInstance(); It works !!!!
      $this->$method($parameter);
    }



    public function index($parm){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

      $modelObj = new genericModel($this->modelName);

      $r = $modelObj->listAll($parm);
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{

        if(sizeof($r) > 0){
  				$this->API->response($this->API->json($r), 200); // send user details
  			}
        $this->API->response('',204);	// If no records "No Content" status

      }

		}

		public function show($parm){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

			if(sizeof($parm) > 0){

        $modelObj = new genericModel($this->modelName);

        $r = $modelObj->findByKey($parm);
        if($modelObj->exceptionOcurred()){
          $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
        }else{
          if(sizeof($r) > 0){
    				$this->API->response($this->API->json($r), 200); // send user details
    			}
    			$this->API->response('',400);	// If no records "No Content" status
        }

			}
			$this->API->response('',400);//204);	// If no records "No Content" status
		}

		public function create($parm){
			if($this->API->get_request_method() != "POST"){
				$this->API->response('',406);
			}

			$entity_data = json_decode(file_get_contents("php://input"),true);

      $modelObj = new genericModel($this->modelName);

      $r = $modelObj->create($parm, $entity_data);
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{
        if(sizeof($r) > 0){
          $this->API->response($this->API->json($r), 200); // send user details
        }
        $this->API->response('',400);	// If no records "No Content" status
      }

		}


		public function update($parm){
			if($this->API->get_request_method() != "PUT"){
				$this->API->response('',406);
			}
			$entity_data = json_decode($this->API->_request, true); //file_get_contents("php://input"),true);

      $modelObj = new genericModel($this->modelName);

      $r = $modelObj->change($parm, $entity_data);
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{
        if(sizeof($r) > 0){
          $this->API->response($this->API->json($r), 200); // send user details
        }
        $this->API->response('',204);	// If no records "No Content" status
      }

		}

		public function destroy($parm){
			if($this->API->get_request_method() != "DELETE"){
				$this->API->response('',406);
			}

			if(sizeof($parm) > 0){

        $modelObj = new genericModel($this->modelName);

        $r = $modelObj->delete($parm);
        if($modelObj->exceptionOcurred()){
          $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
        }else{
          if(sizeof($r) > 0){
            $this->API->response($this->API->json($r), 200); // send user details
          }
          $this->API->response('',204);	// If no records "No Content" status
        }

			}else
				$this->API->response('',204);	// If no records "No Content" status
		}


}

?>
