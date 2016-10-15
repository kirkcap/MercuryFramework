<?php
namespace com\mercuryfw\controllers;
require_once(__ROOT__."/backend/api/model/genericModel.php");
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\models\genericModel as genericModel;

class genericCRUDController{

    protected $API;
    protected $modelName;
    protected $filter;
    protected $pagination;
    protected $sort;


    public function __construct($ModelName){

      $this->API = REST::getInstance();
      $this->modelName = $ModelName;

    }

    public function execute($method, $parameter, $filter_pagination_sort=[]){
      if(sizeOf($filter_pagination_sort)>0){
        $this->setFilter($filter_pagination_sort['filter_criteria']);
        $this->setPagination($filter_pagination_sort['pagination']);
        $this->setSort($filter_pagination_sort['sort']);
      }
      $this->$method($parameter);
    }

    public function setFilter($filter){
      $this->filter = $filter;
    }
    public function setPagination($pagination){
      $this->pagination = $pagination;
    }
    public function setSort($sort){
      $this->sort = $sort;
    }


    public function index($parm){
			if($this->API->get_request_method() != "GET"){
				$this->API->response('',406);
			}

      $modelObj = new genericModel($this->modelName);

      $r = $modelObj->listAll($parm, $this->filter, $this->pagination, $this->sort);
      if($modelObj->exceptionOcurred()){
        $this->API->response($modelObj->getErrorData()->getFrontEndResponse(),200);
      }else{

        if(sizeof($r) > 0){
          $additional_headers = [];
          $additional_headers[0] = ['name' => 'Content-Range', 'value' => $r['range_low'].'-'.$r['range_high'].'/'.$r['records_count']];
          $additional_headers[1] = ['name' => 'Accept-Range', 'value' => $this->modelName.' '.$r['max_size']];
          $http_code = 200;
          if($r['range_size'] < $r['records_count']){//If the range size is less than total records, return http 206 - partial content
            $http_code = 206;
          }
          $this->API->response($this->API->json($r['data']), $http_code, $additional_headers); // send user details
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
