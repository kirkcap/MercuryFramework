<?php

class ControllerFactory{
  public static function getController($controller_class_name, $construct_parameters_array){
    $reflectionClass = new ReflectionClass($controller_class_name);
    return $reflectionClass->newInstanceArgs($construct_parameters_array);
  }
}
 ?>
