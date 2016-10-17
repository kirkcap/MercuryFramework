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

class ControllerFactory{
  public static function getController($controller_class_name, $construct_parameters_array){
    $reflectionClass = new \ReflectionClass('com\\mercuryfw\\controllers\\'.$controller_class_name);
    return $reflectionClass->newInstanceArgs($construct_parameters_array);
  }
}
 ?>
