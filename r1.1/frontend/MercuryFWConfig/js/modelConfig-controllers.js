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
.controller('ModelConfigCreateController', function($state, $stateParams, CONFIG_API, ConfigMetadata, ngDialog) {
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
  vm.config_body = vm.metadata.schema; //Used only on complex parameters
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
                        if (confirm('Are you sure you want to close without saving your changes?')) {
                            return true;
                        }
                        return false;
                      }
                    }
                   });

    dialog.closePromise.then(function (data) {
        alert(data.id + ' has been dismissed.');
        if(!vm.continue){
          vm.config_body = {};
          history.go(-1);
        }
    });
  };

  vm.newModelDialog();//Calls the dialog to fill Model basic configuration data


  vm.cfgDialog = function(cfg) {

    vm.detail_key = cfg.key;
    ngDialog.open({ template: 'partials/dialog/'+ vm.metadata.meta[vm.detail_key].dialogForm, //cfgDialogTbColumnsEdit.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: vm.metadata.meta[vm.detail_key].dialogClass, //'ngdialog-custom',
                    width: '98%',
                    controller: vm.metadata.meta[vm.detail_key].dialogController, //'CfgDialogEditController as dialogVm',
                    data: vm });

  };


}).controller('ModelConfigViewEditController', function($state, $stateParams, $filter, CONFIG_API, ConfigMetadata, ngDialog) {
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
        })
      }
    });

  };

  vm.cfgDialog = function(cfg) {

    vm.detail_key = cfg.key;
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
    vm.tableStructure = DBTableStructure.get({db_name:vm.data.config_body.dbCfgName, tb_name:tb_name});
    angular.forEach(vm.modelConfigs,function(data, model_name){
      if(data.tb_name==tb_name){
        vm.existingModels[$i++] = model_name;
      }
    });
  }

})
