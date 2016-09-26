angular.module('mercuryFWConfigApp.controllers',[])

.controller('GenericConfigListController', function($state, popupService, $window, CONFIG_API, ConfigMetadata) {
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
/*Substituted by the GenericConfigViewEditController, which does both actions, according to parameter set in the state configuration
.controller('GenericConfigViewController', function( $state, $stateParams, CONFIG_API, Utils, ConfigMetadata) {
  var vm = this;

  vm.state_data = $state.current;
  vm.metadata = ConfigMetadata[vm.state_data.cfgname];
  vm.config_key = {"value" : $stateParams.cfg };
  vm.config = CONFIG_API.get({ service: vm.metadata.svcname, cfg: vm.config_key.value }, function(){
    if(!vm.metadata.singleTypeParameters){
      vm.config_body = vm.config[vm.config_key.value];
    }
  }); //Get a single attribute.Issues a GET to /api/attributes/:id

})
*/
.controller('GenericConfigCreateController', function($state, $stateParams, CONFIG_API, ConfigMetadata) {
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
  if(!vm.metadata.singleTypeParameters){
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
  }
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

}).controller('GenericConfigViewEditController', function($state, $stateParams, $filter, CONFIG_API, ConfigMetadata, ngDialog) {
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

}).controller('CfgTbColumnsDialogEditController', function($scope, ngDialog){
  var vm = this;
  vm.data = $scope.ngDialogData;
  vm.data.others = {};

  vm.cfgSubDialog = function(cfg) {
    var data = {};
    data.action     = cfg.action;
    data.field      = cfg.field;
    data.detail_key = cfg.attribute;
    data.config_body = cfg.data;
    data.metadata = {};
    data.metadata.meta = $scope.ngDialogData.metadata.meta.tb_columns.structure.field.structure;
    ngDialog.open({ template: 'partials/dialog/'+data.metadata.meta[data.detail_key].dialogForm, //cfgTbColumnsFieldDefaultDialogEdit.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: data.metadata.meta[data.detail_key].dialogClass, //'ngdialog-custom',
                    width: '80%',
                    controller: data.metadata.meta[data.detail_key].dialogController, //'CfgDialogEditController as subDialogVm',
                    data: data });
  };

}).controller('CfgTbColumnsFieldDefaultDialogEditController', function($scope, ngDialog){
  var vm = this;
  vm.data = $scope.ngDialogData;
  vm.data.others = {};

})
