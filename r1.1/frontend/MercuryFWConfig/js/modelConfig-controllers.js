angular.module('mercuryFWConfigApp.controllers')

.controller('ModelConfigListController', function($state, popupService, $window, CONFIG_API, ConfigMetadata) {
  var vm = this;
  vm.state_data = $state.current;
  vm.metadata = ConfigMetadata[vm.state_data.cfgname];
  vm.configs = CONFIG_API.query({ service: vm.metadata.svcname }); //fetch all attributes. Issues a GET to /api/attributes

  vm.deleteConfig = function(cfg) { // Delete an attribute. Issues a DELETE to /api/attributes/:id

    if (popupService.showPopup('Really delete this?')) {
      vm.config = CONFIG_API.get({ service: vm.metadata.svcname, cfg:cfg.key }, function() {
        vm.config.$delete({ service: vm.metadata.svcname, cfg:cfg.key }, function() {
          window.location.reload();
        });
      });
    }

  };

})
.controller('ModelConfigCreateController', function($state, $stateParams, CONFIG_API, ConfigMetadata, ngDialog, Utils, ModelMetadataLoader) {
  var vm = this;

  vm.state_data = $state.current;
  vm.metadata = ConfigMetadata[vm.state_data.cfgname];

  if(vm.metadata.form_include){
    vm.form_include = vm.metadata.form_include;
  }else{
    if(vm.metadata.singleTypeParameters){
      vm.form_include = "generic_cfg_simple_form.html";
    }else{
      vm.form_include = "generic_cfg_form.html";
    }
  }

  vm.action = "new";
  vm.config_key = {"value":"","placeholder":vm.metadata.meta['param_key'].placeholder};
  vm.config_body = JSON.parse( JSON.stringify(vm.metadata.schema)); //Used only on complex parameters
  angular.forEach(vm.config_body, function(value, key){
    if(vm.metadata.meta[key].fldtype == "select" && vm.metadata.meta[key].valid_values.type=="service_call"){
      vm.cfg_values = CONFIG_API.query({ service: vm.metadata.meta[key].valid_values.service }, function(){
        $i=0;
        vm.metadata.meta[key].valid_values.values = [];
        angular.forEach(vm.cfg_values, function(cfg_value, cfg_key){
          vm.metadata.meta[key].valid_values.values[$i++] = {key: cfg_key, value: ""};
        })
        vm.cfg_values={};
      })
    }
  })
  vm.config = new CONFIG_API();  //create new attribute instance. Properties will be set via ng-model on UI
  vm.others = {};//Variable to receive "Other" select values

  vm.addConfig = function() { //create a new attribute. Issues a POST to /api/attributes
    if(!vm.metadata.singleTypeParameters){
      vm.config[vm.config_key.value] = vm.config_body; //Used only in complex parameters
      angular.forEach(vm.config_body, function(value, key){
        if(vm.metadata.meta[key].fldtype == "select" && value == "Other"){
          vm.config[vm.config_key.value][key] = vm.others[key];
        }
      })
    }

    vm.config.$save({ service: vm.metadata.svcname, cfg: vm.config_key.value }, function() {
      $state.go(vm.metadata.sourceUrl);//'teste'); // on success go back to home i.e. attributes state.
    });
  };

  /*vm.buildTbColumns = function(){
    vm.config_body.tb_columns = vm.metadata.tb_columns_schema;
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
    * /
    for($i=0;$i<vm.tableStructure.length;$i++){
      vm.config_body.tb_columns[vm.tableStructure[$i].Field] = JSON.parse(JSON.stringify(vm.metadata.tb_columns_field_schema));
      angular.forEach(vm.tableStructure[$i], function(data,key){
        lkey = key.toLowerCase();
        switch(lkey){
          case 'type':
            vm.config_body.tb_columns[vm.tableStructure[$i].Field].dbtype = data;
            vm.config_body.tb_columns[vm.tableStructure[$i].Field].bind_type = Utils.getBindType(data);
            break;
          case 'key':
            if(data=='PRI'){
              vm.config_body.tb_columns[vm.tableStructure[$i].Field].key = true;
              vm.config_body.tb_columns[vm.tableStructure[$i].Field].update = false;
              vm.config_body.tb_key[vm.config_body.tb_key.length] = vm.tableStructure[$i].Field;
            }else{
              vm.config_body.tb_columns[vm.tableStructure[$i].Field].key = false;
            }
            break;
          case 'comment':
            vm.config_body.tb_columns[vm.tableStructure[$i].Field].label = data;
            break;
        }
      });
    }
    alert('Structure building complete !');
  }*/

  vm.newModelDialog = function() {
    vm.continue = false;
    var dialog = ngDialog.open({ template: 'partials/dialog/newModelCfgDialog.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: 'ngdialog-custom-new_model',
                    width: '90%',
                    closeByDocument: false,
                    controller: 'CfgDialogNewModelController as newModelVM',
                    data: vm,
                    cache: false,
                    preCloseCallback: function(value) {
                      if(!vm.continue){
                        if (confirm('Data filled will be lost, are you sure you want to close ?')) {
                            return true;
                        }
                        return false;
                      }
                    }
                   });

    dialog.closePromise.then(function (data) {
        //alert(data.id + ' has been dismissed.');
        if(!vm.continue){
          vm.config_body = {};
          history.go(-1);
        }else{
          //vm.buildTbColumns();
          ModelMetadataLoader.modelStructurePrepare(vm.config_body, vm.metadata);
          //var $updated_config_body = ModelMetadataLoader.modelStructurePrepare(vm.config_body, vm.metadata, function(){
          //  vm.config_body = $updated_config_body;
          //});
        }
    });
  };

  vm.newModelDialog();//Calls the dialog to fill Model basic configuration data


  vm.cfgDialog = function(cfg) {

    vm.detail_key = cfg.key;
    var vmeta = vm.metadata.meta[cfg.key].structure.field.structure
    vm.metadata.meta[cfg.key].structure.field.filteredStructure = {};
    angular.forEach(vmeta, function(meta, key){
      if(!meta.noShow){
        vm.metadata.meta[cfg.key].structure.field.filteredStructure[key] = vmeta[key];
      }
    });

    ngDialog.open({ template: 'partials/dialog/'+ vm.metadata.meta[vm.detail_key].dialogForm, //cfgDialogTbColumnsEdit.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: vm.metadata.meta[vm.detail_key].dialogClass, //'ngdialog-custom',
                    width: '98%',
                    cache: false,
                    controller: vm.metadata.meta[vm.detail_key].dialogController, //'CfgDialogEditController as dialogVm',
                    data: vm });

  };


}).controller('ModelConfigViewEditController', function($state, $stateParams, $filter, CONFIG_API, ConfigMetadata, ngDialog, ModelMetadataLoader) {
  var vm = this;

  vm.state_data = $state.current;
  vm.metadata = ConfigMetadata[vm.state_data.cfgname]; //Gets metadata from constants

  if(vm.metadata.form_include){ //Set the form include to be used
    vm.form_include = vm.metadata.form_include;
  }else{
    if(vm.metadata.singleTypeParameters){
      vm.form_include = "generic_cfg_simple_form.html";
    }else{
      vm.form_include = "generic_cfg_form.html";
    }
  }

  vm.action = vm.state_data.action;//Gets action from state configuration
  vm.config_key = {"value" : $stateParams.cfg };
  vm.others = {};//Variable to receive "Other" select values

  vm.updateConfig = function() { //Update the edited attribute. Issues a PUT to /api/attributes/:id
    if(!vm.metadata.singleTypeParameters){
      vm.config[vm.config_key.value] = vm.config_body; //Used only in complex parameters
      angular.forEach(vm.config_body, function(value, key){
        if(vm.metadata.meta[key].fldtype == "select" && value == "Other"){
          vm.config[vm.config_key.value][key] = vm.others[key];
        }
      })
    }
    vm.config.$update({ service: vm.metadata.svcname, cfg: vm.config_key.value }, function() {
      $state.go(vm.metadata.sourceUrl);//'teste'); // on success go back to home i.e. attributes state.
    });
  };

  vm.loadConfig = function() { //Issues a GET request to /api/attributes/:id to get a attribute to update

    vm.config = CONFIG_API.get({ service: vm.metadata.svcname, cfg: vm.config_key.value }, function(){
      if(!vm.metadata.singleTypeParameters){
        vm.config_body = vm.config[vm.config_key.value];//Used only in complex parameters

        angular.forEach(vm.config_body, function(value, key){
          if(vm.metadata.meta[key].fldtype == "select" && vm.metadata.meta[key].valid_values.type=="service_call"){
            vm.cfg_values = CONFIG_API.query({ service: vm.metadata.meta[key].valid_values.service }, function(){
              $i=0;
              vm.metadata.meta[key].valid_values.values = [];
              angular.forEach(vm.cfg_values, function(cfg_value, cfg_key){
                if(cfg_key.substr(0,1)!="$"){
                  vm.metadata.meta[key].valid_values.values[$i++] = {key: cfg_key, value: ""};
                }
              })
              //if(vm.metadata.meta[key].valid_values.values.indexOf(value) == -1 ){
              if($filter('filter')(vm.metadata.meta[key].valid_values.values, value, true).length==0){
                vm.others[key] = vm.config_body[key];
                vm.config_body[key] = "Other";
              }
              vm.cfg_values={};
            })
          }
        })

        angular.forEach(vm.config_body, function(value, key){
          //if(vm.metadata.meta[key].fldtype == "select" && vm.metadata.meta[key].valid_values.values.indexOf(value) == -1 ){
          if(vm.metadata.meta[key].fldtype == "select" && vm.metadata.meta[key].valid_values.type=="array"){
            if($filter('filter')(vm.metadata.meta[key].valid_values.values, value, true).length==0){
              vm.others[key] = vm.config_body[key];
              vm.config_body[key] = "Other";
            }
          }
        });
        ModelMetadataLoader.modelStructurePrepare(vm.config_body, vm.metadata);
      }
    });

  };

  vm.cfgDialog = function(cfg) {

    vm.detail_key = cfg.key;

    var vmeta = vm.metadata.meta[cfg.key].structure.field.structure
    vm.metadata.meta[cfg.key].structure.field.filteredStructure = {};
    angular.forEach(vmeta, function(meta, key){
      if(!meta.noShow){
        vm.metadata.meta[cfg.key].structure.field.filteredStructure[key] = vmeta[key];
      }
    });

    ngDialog.open({ template: 'partials/dialog/'+ vm.metadata.meta[vm.detail_key].dialogForm, //cfgDialogTbColumnsEdit.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: vm.metadata.meta[vm.detail_key].dialogClass, //'ngdialog-custom',
                    width: '98%',
                    controller: vm.metadata.meta[vm.detail_key].dialogController, //'CfgDialogEditController as dialogVm',
                    data: vm });

  };


  vm.loadConfig(); // Load a attribute which can be edited on UI


}).controller('CfgDialogNewModelController', function($scope, ConfigMetadata, CONFIG_API, DBTableStructure){
  var vm = this;
  vm.data = $scope.ngDialogData;
  vm.data.others = {};
  var db_config_metadata = ConfigMetadata['DbConfig'];
  var model_config_metadata = ConfigMetadata['ModelConfig'];
  vm.dbConfigs = CONFIG_API.query({service:db_config_metadata.svcname});
  vm.modelConfigs = CONFIG_API.query({service:model_config_metadata.svcname});
  vm.existingModels = [];

  vm.getDbTablesList = function(dbCfgName){
    vm.dbTables = CONFIG_API.get({service:'dbMetadata',cfg:dbCfgName});
  }

  vm.checkExistingModels = function(tb_name){
    $i=0;
    vm.existingModels = [];
    vm.data.tableStructure = DBTableStructure.get({db_name:vm.data.config_body.dbCfgName, tb_name:tb_name});
    angular.forEach(vm.modelConfigs,function(data, model_name){
      if(data.tb_name==tb_name && ((!data.dbCfgName && vm.data.config_body.dbCfgName =='default') || (data.dbCfgName==vm.data.config_body.dbCfgName))){
        vm.existingModels[$i++] = model_name;
      }
    });
  }

})
