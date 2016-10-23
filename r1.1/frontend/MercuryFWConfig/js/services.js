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
* Angular JS
*
* @category Services
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

angular.module('mercuryFWConfigApp.services', [])

.factory('CONFIG_API', function($resource, BackendConfig) {
  return $resource(BackendConfig.url + ':service/:cfg', { service: '@service', cfg: '@cfg' }, {
    update: {method:'PUT', params: { service: '@service', cfg: '@cfg' }},
    get:    {method:'GET', params: { service: '@service', cfg: '@cfg' }},
    save:   {method:'POST', params: { service: '@service', cfg: '@cfg' }},
    query:  {method:'GET', isArray:false, params: { service: '@service' }},
    remove: {method:'DELETE', params: { service: '@service', cfg: '@cfg' }},
    delete: {method:'DELETE', params: { service: '@service', cfg: '@cfg' }}
  });
})

.factory('DBTableStructure', function($resource, BackendConfig) {
  return $resource(BackendConfig.url + 'dbMetadata/:db_name/tbMetadata/:tb_name', { db_name: '@db_name', tb_name: '@tb_name' }, {
    get:    {method:'GET', isArray:true, params: { db_name: '@db_name', tb_name: '@tb_name' }}
    //query:  {method:'GET', isArray:false, params: { service: '@service' }},
  });
})

.factory('ModelMetadataLoader', function(DBTableStructure, Utils){
  return {
    modelStructurePrepare: function($config_body, $metadata){
      var tableStructure = DBTableStructure.get({db_name:$config_body.db_cfg_name, tb_name:$config_body.tb_name},function(){
        if(!$config_body.tb_columns){
          $config_body.tb_columns = $metadata.tb_columns_schema;
        }
        /*Sample of Field definition:
          [{
            "Field": "attcod",
            "Type": "int(11) unsigned",
            "Collation": null,
            "Null": "NO",
            "Key": "PRI",
            "Default": null,
            "Extra": "",
            "Privileges": "select,insert,update",
            "Comment": "Attribute Code"
          },
          ...]
        */
        $config_body.tb_key = [];
        for($i=0;$i<tableStructure.length;$i++){
          if(!$config_body.tb_columns[tableStructure[$i].Field]){
            $new = true;
            $config_body.tb_columns[tableStructure[$i].Field] = JSON.parse(JSON.stringify($metadata.tb_columns_field_schema));
          }else{
            $new = false;
          }
          angular.forEach(tableStructure[$i], function(data,key){
            lkey = key.toLowerCase();
            switch(lkey){
              case 'type':
                $config_body.tb_columns[tableStructure[$i].Field].dbtype = data;
                $config_body.tb_columns[tableStructure[$i].Field].bind_type = Utils.getBindType(data);
                break;
              case 'key':
                if(data=='PRI'){
                  $config_body.tb_columns[tableStructure[$i].Field].key = true;
                  if($new){
                    $config_body.tb_columns[tableStructure[$i].Field].update = false;
                  }
                  $config_body.tb_key[$config_body.tb_key.length] = tableStructure[$i].Field;
                }else{
                  $config_body.tb_columns[tableStructure[$i].Field].key = false;
                }
                break;
              case 'comment':
                if($new || !$config_body.tb_columns[tableStructure[$i].Field].label || $config_body.tb_columns[tableStructure[$i].Field].label == ""){
                  $config_body.tb_columns[tableStructure[$i].Field].label = data;
                }
                break;
            }
          });
        }
        var $deletedFields = [];
        angular.forEach($config_body.tb_columns, function(meta, key){
          var $found = false;
          for($i=0;$i<tableStructure.length;$i++){
            if(tableStructure[$i].Field == key){
              $found = true;
              break;//Exits the loop for
            }
          }
          if(!$found){
            $deletedFields[$deletedField.length] = key;
            delete $config_body.tb_columns[key];
          }
        });
        if($deletedFields.length>0){
          var $delFieldsList="";
          var $sep = "";
          for($i=0;$i<$deletedFields.length;$i++){
            $delFieldsList += $sep + $deletedFields[$i];
            $sep = ", ";
          }
          alert('The following fields where deleted from the model(as they are not present in the table structure anymore):'+$delFieldsList);
        }
        //return $config_body;
      });
    }
  }
})

.factory('Utils', function(DBTypes_X_BindTypes){
  return {
    getBindType: function(data_type){

      if(data_type.indexOf('(') >= 0){
        dtype = data_type.substr(0,data_type.indexOf('(')).toLowerCase();
      }else{
        dtype = data_type;
      }
      if(DBTypes_X_BindTypes[dtype]){
        return DBTypes_X_BindTypes[dtype];
      }else{
        return DBTypes_X_BindTypes['default'];
      }
    },

    isSingleType: function(data){
      $data_type = typeof data;
      $data_type = $data_type.toUpperCase();
      switch($data_type){
        case "STRING":
          return true;
          break;
        case "NUMBER":
          return true;
          break;
        case "BOOLEAN":
          return true;
          break;
        case "OBJECT":
          return false;
          break;
        case "ARRAY":
          return false;
          break;
      }
    }
  }
})

.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});
