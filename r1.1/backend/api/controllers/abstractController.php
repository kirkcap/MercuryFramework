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
use com\mercuryfw\helpers\REST as REST;
use com\mercuryfw\models\genericModel as genericModel;

interface ifController{
  public function execute($method, $parameter, $filter_pagination_sort);
  public function index($parm); //For HTTP GET - Get all records
  public function show($parm); //For HTTP GET - Get specific record
  public function create($parm); //For HTTP POST - Create new record
  public function update($parm); //For HTTP PUT - Update record
  public function destroy($parm); //For HTTP DELETE - Delete record
}

abstract class abstractController implements ifController{

    protected $API;
    protected $modelName;
    protected $filter;
    protected $pagination;
    protected $sort;

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
    public function getFilter(){
      return $this->filter;
    }

    public function setPagination($pagination){
      $this->pagination = $pagination;
    }
    public function getPagination(){
      return $this->pagination;
    }

    public function setSort($sort){
      $this->sort = $sort;
    }
    public function getSort(){
      return $this->sort;
    }

}

?>
