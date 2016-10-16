<?php
namespace com\mercuryfw\controllers;
require_once(__ROOT__."/backend/api/model/dbMetadataModel.php");
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\models\dbMetadataModel as dbMetadataModel;

class dbMetadataController{

    protected $API;
    protected $dbCfgName;


    public function __construct($dbCfgName){

      $this->API = REST::getInstance();
      $this->dbCfgName = $dbCfgName;

    }

    public function execute($method, $parameter){
      $this->$method($parameter);
    }



    public function getDBMetadata(){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

      $modelObj = new dbMetadataModel($this->dbCfgName);

      $r = $modelObj->listAll();
      if($modelObj->exceptionOcurred()){
        $this->API->response($this->API->json($modelObj->getErrorData()->getFrontEndResponse()),200);
      }else{

        if(sizeof($r) > 0){
  				$this->API->response($this->API->json($r), 200); // send user details
  			}
        $this->API->response('',204);	// If no records "No Content" status

      }

		}

		public function getTBMetadata($key){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

			if(sizeof($key) > 0){
        $modelObj = new dbMetadataModel($this->dbCfgName);

        $r = $modelObj->findByKey($key[1]);

        if($modelObj->exceptionOcurred()){
          $this->API->response($this->API->json($modelObj->getErrorData()->getFrontEndResponse()),200);
        }else{
          if(sizeof($r) > 0){
            $this->API->response($this->API->json($r), 200); // send user details
    			}
    			$this->API->response('',200);	// If no records "No Content" status
        }

			}
			$this->API->response('',200);//204);	// If no records "No Content" status
		}

		public function create($key){
			if($this->API->get_request_method() != "POST"){
				$this->API->response('',406);
			}

			$this->API->response('{"Method not available"}',200);	// If no records "No Content" status

		}


		public function update($key){
			if($this->API->get_request_method() != "PUT"){
				$this->API->response('',406);
			}

			$this->API->response('{"Method not available"}',200);	// If no records "No Content" status

		}

		public function destroy($key){
			if($this->API->get_request_method() != "DELETE"){
				$this->API->response('',406);
			}

			$this->API->response('{"Method not available"}',200);	// If no records "No Content" status
		}


}

?>
