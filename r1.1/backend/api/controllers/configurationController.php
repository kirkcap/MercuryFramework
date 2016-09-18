<?php
namespace com\mercuryfw\controllers;
require_once(__ROOT__."/backend/api/model/configModel.php");
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\models\configModel as configModel;

class configurationController{

    protected $API;
    protected $cfgName;


    public function __construct($CfgName){

      $this->API = REST::getInstance();
      $this->cfgName = $CfgName;

    }

    public function execute($method, $parameter){
      //$token = Token::getInstance(); It works !!!!
      $this->$method($parameter);
    }



    public function index(){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

      $modelObj = new configModel($this->cfgName);

      $r = $modelObj->listAll();
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{

        if(sizeof($r) > 0){
  				$this->API->response($this->API->json($r), 200); // send user details
  			}
        $this->API->response('',204);	// If no records "No Content" status

      }

		}

		public function show($key){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

			if(sizeof($key) > 0){
        $modelObj = new configModel($this->cfgName);

        $r = $modelObj->findByKey($key[0]);

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

		public function create($key){
			if($this->API->get_request_method() != "POST"){
				$this->API->response('',406);
			}

			$entity_data = json_decode($this->API->_request, true); //file_get_contents("php://input"),true);

      $modelObj = new configModel($this->cfgName);

      $r = $modelObj->create($key[0], $entity_data);
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{
        if(sizeof($r) > 0){
          $this->API->response($this->API->json($r), 200); // send user details
        }
        $this->API->response('',400);	// If no records "No Content" status
      }

		}


		public function update($key){
			if($this->API->get_request_method() != "PUT"){
				$this->API->response('',406);
			}
			$entity_data = json_decode($this->API->_request, true); //$this->API->_request, true); //file_get_contents("php://input"),true);

      $modelObj = new configModel($this->cfgName);

      $r = $modelObj->change($key[0], $entity_data);
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{
        if(sizeof($r) > 0){
          $this->API->response($this->API->json($r), 200); // send user details
        }
        $this->API->response('',204);	// If no records "No Content" status
      }

		}

		public function destroy($key){
			if($this->API->get_request_method() != "DELETE"){
				$this->API->response('',406);
			}

			if(sizeof($key) > 0){

        $modelObj = new configModel($this->cfgName);

        $r = $modelObj->delete($key[0]);
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
