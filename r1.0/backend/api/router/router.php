<?php
require_once("baseRouter.php");
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
class router extends baseRouter{

  protected $routes = array(
//  "object"              => array("controller" => "<controller>","method" => "<method>", "checkToken" => true/false, ["model" => "<model>"]) where method = CRUD|Method Name, model=Model Name
    "attributes"                => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "attributesModel"),
    "attributes.values"         => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "attributeValuesModel"),
    "classtypes"                => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "classTypesModel"),
    "classes"                   => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "classesModel"),
    "classes.attributes"        => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "classAttributesModel"),
    "expensetypes"              => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "expenseTypesModel"),
    "makers"                    => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "makersModel"),
    "makers.models"             => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "makerModelsModel"),
    "monexpenses"               => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "monExpHeaderModel"),
    "monexpensesvw"             => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "monExpHeaderModelVW"),
    "monexpenses.items"         => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "monExpItemsModel"),
    "objects.classes"           => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "objectClassesModel"),
    "objects.classes.attvals"   => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "objectAttValuesModel"),
    "objects.classes.attvalsvw" => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "objectAttValuesModelVW"),
    "serviceorders"             => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "serviceOrdersModel"),
    "serviceorders.items"       => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "serviceOrderItemsModel"),
    "vehicles"                  => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "vehiclesModel"),
    "vehiclesvw"                => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "vehiclesModelVW"),
    "vehicles.apportionmts"     => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "vehicleApportionmentsModel"),
    "vehicletypes.subtypes"     => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "vehicleSubtypesModel"),
    "vehicletypes"              => array("controller" => "genericCRUDController","method" => "CRUD" , "checkToken" => false, "model" => "vehicleTypesModel"),
    "users"                     => array("controller" => "genericAuthController","method" => "CRUD" , "checkToken" => false, "model" => "authModel"),
    "login"                     => array("controller" => "genericAuthController","method" => "login", "checkToken" => false, "model" => "authModel")
  );

}
?>
