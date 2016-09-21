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

}).controller('GenericConfigViewController', function( $state, $stateParams, CONFIG_API, Utils, ConfigMetadata) {
  var vm = this;

  vm.state_data = $state.current;
  vm.metadata = ConfigMetadata[vm.state_data.cfgname];
  vm.config_key = {"value" : $stateParams.cfg };
  vm.config = CONFIG_API.get({ service: vm.metadata.svcname, cfg: vm.config_key.value }, function(){
    if(!$state.current.singleTypeParameters){
      vm.config_body = vm.config[vm.config_key.value];
    }
  }); //Get a single attribute.Issues a GET to /api/attributes/:id

}).controller('GenericConfigCreateController', function($state, $stateParams, CONFIG_API, ConfigMetadata) {
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
  if(!$state.current.singleTypeParameters){
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
    if(!$state.current.singleTypeParameters){
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

}).controller('GenericConfigEditController', function($state, $stateParams, $filter, CONFIG_API, ConfigMetadata, ngDialog) {
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

  vm.action = "edit";
  vm.config_key = {"value" : $stateParams.cfg };
  vm.others = {};//Variable to receive "Other" select values

  vm.updateConfig = function() { //Update the edited attribute. Issues a PUT to /api/attributes/:id
    if(!$state.current.singleTypeParameters){
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
      if(!$state.current.singleTypeParameters){
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
    ngDialog.open({ template: 'partials/dialog/cfgDialogEdit.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: 'ngdialog-custom',
                    width: '98%',
                    className: 'ngdialog-theme-default',
                    controller: 'CfgDialogEditController as dialogVm',
                    data: vm });
  };


  vm.loadConfig(); // Load a attribute which can be edited on UI

}).controller('CfgDialogEditController', function($scope){
  var vm = this;

  vm.saveConfig = function(){
    alert('Clicou !');
  }

  vm.cfgSubDialog = function(cfg) {
    var data = {};
    data.detail_key = cfg.key;
    data.config_body = cfg.data;
    ngDialog.open({ template: 'partials/dialog/cfgDialogEdit.html',
                    className: 'ngdialog-theme-default',
                    appendClassName: 'ngdialog-custom',
                    width: '98%',
                    className: 'ngdialog-theme-default',
                    controller: 'CfgDialogEditController as subDialogVm',
                    data: data });
  };

})
