angular.module('mercuryFWConfigApp.controllers',[])

.controller('GenericConfigListController', function($scope, $state, popupService, $window, CONFIG_API) {
  $scope.state_data = $state.current;
  $scope.configs = CONFIG_API.query({ service: $state.current.svcname }); //fetch all attributes. Issues a GET to /api/attributes

  $scope.deleteConfig = function(cfg) { // Delete an attribute. Issues a DELETE to /api/attributes/:id

    if (popupService.showPopup('Really delete this?')) {
      $scope.config = CONFIG_API.get({ service: $state.current.svcname, cfg:cfg.key }, function() {
        $scope.config.$delete({ service: $state.current.svcname, cfg:cfg.key }, function() {
          window.location.reload();
        });
      });
    }

  };

}).controller('GenericConfigViewController', function($scope, $state, $stateParams, CONFIG_API, Utils) {
  $scope.state_data = $state.current;
  $scope.config_key = {"value" : $stateParams.cfg };
  $scope.config = CONFIG_API.get({ service: $state.current.svcname, cfg: $scope.config_key.value }, function(){
    if(!$state.current.singleTypeParameters){
      $scope.config_body = $scope.config[$scope.config_key.value];
    }
  }); //Get a single attribute.Issues a GET to /api/attributes/:id

}).controller('GenericConfigCreateController', function($scope, $state, $stateParams, CONFIG_API, ConfigMetadata) {
  $scope.state_data = $state.current;
  $scope.metadata = ConfigMetadata[$scope.state_data.cfgname];

  if($scope.state_data.form_include){
    $scope.form_include = $scope.state_data.form_include;
  }else{
    if($scope.state_data.singleTypeParameters){
      $scope.form_include = "generic_cfg_simple_form.html";
    }else{
      $scope.form_include = "generic_cfg_form.html";
    }
  }

  $scope.action = "new";
  $scope.config_key = {"value":"","placeholder":$scope.metadata.meta['param_key'].placeholder};
  $scope.config_body = $scope.metadata.schema; //Used only on complex parameters
  if(!$state.current.singleTypeParameters){
    angular.forEach($scope.config_body, function(value, key){
      if($scope.metadata.meta[key].type == "select" && $scope.metadata.meta[key].valid_values.type=="service_call"){
        $scope.cfg_values = CONFIG_API.query({ service: $scope.metadata.meta[key].valid_values.service }, function(){
          $i=0;
          $scope.metadata.meta[key].valid_values.values = [];
          angular.forEach($scope.cfg_values, function(cfg_value, cfg_key){
            $scope.metadata.meta[key].valid_values.values[$i++] = {key: cfg_key, value: ""};
          })
          $scope.cfg_values={};
        })
      }
    })
  }
  $scope.config = new CONFIG_API();  //create new attribute instance. Properties will be set via ng-model on UI
  $scope.others = {};//Variable to receive "Other" select values

  $scope.addConfig = function() { //create a new attribute. Issues a POST to /api/attributes
    if(!$state.current.singleTypeParameters){
      $scope.config[$scope.config_key.value] = $scope.config_body; //Used only in complex parameters
      angular.forEach($scope.config_body, function(value, key){
        if($scope.metadata.meta[key].type == "select" && value == "Other"){
          $scope.config[$scope.config_key.value][key] = $scope.others[key];
        }
      })
    }

    $scope.config.$save({ service: $state.current.svcname, cfg: $scope.config_key.value }, function() {
      $state.go($state.current.sourceUrl);//'teste'); // on success go back to home i.e. attributes state.
    });
  };

}).controller('GenericConfigEditController', function($scope, $state, $stateParams, $filter, CONFIG_API, ConfigMetadata) {
  $scope.state_data = $state.current;
  $scope.metadata = ConfigMetadata[$scope.state_data.cfgname];

  if($scope.state_data.form_include){
    $scope.form_include = $scope.state_data.form_include;
  }else{
    if($scope.state_data.singleTypeParameters){
      $scope.form_include = "generic_cfg_simple_form.html";
    }else{
      $scope.form_include = "generic_cfg_form.html";
    }
  }

  $scope.action = "edit";
  $scope.config_key = {"value" : $stateParams.cfg };
  $scope.others = {};//Variable to receive "Other" select values

  $scope.updateConfig = function() { //Update the edited attribute. Issues a PUT to /api/attributes/:id
    if(!$state.current.singleTypeParameters){
      $scope.config[$scope.config_key.value] = $scope.config_body; //Used only in complex parameters
      angular.forEach($scope.config_body, function(value, key){
        if($scope.metadata.meta[key].type == "select" && value == "Other"){
          $scope.config[$scope.config_key.value][key] = $scope.others[key];
        }
      })
    }
    $scope.config.$update({ service: $state.current.svcname, cfg: $scope.config_key.value }, function() {
      $state.go($state.current.sourceUrl);//'teste'); // on success go back to home i.e. attributes state.
    });
  };

  $scope.loadConfig = function() { //Issues a GET request to /api/attributes/:id to get a attribute to update

    $scope.config = CONFIG_API.get({ service: $state.current.svcname, cfg: $scope.config_key.value }, function(){
      if(!$state.current.singleTypeParameters){
        $scope.config_body = $scope.config[$scope.config_key.value];//Used only in complex parameters

        angular.forEach($scope.config_body, function(value, key){
          if($scope.metadata.meta[key].type == "select" && $scope.metadata.meta[key].valid_values.type=="service_call"){
            $scope.cfg_values = CONFIG_API.query({ service: $scope.metadata.meta[key].valid_values.service }, function(){
              $i=0;
              $scope.metadata.meta[key].valid_values.values = [];
              angular.forEach($scope.cfg_values, function(cfg_value, cfg_key){
                if(cfg_key.substr(0,1)!="$"){
                  $scope.metadata.meta[key].valid_values.values[$i++] = {key: cfg_key, value: ""};
                }
              })
              //if($scope.metadata.meta[key].valid_values.values.indexOf(value) == -1 ){
              if($filter('filter')($scope.metadata.meta[key].valid_values.values, value, true).length==0){
                $scope.others[key] = $scope.config_body[key];
                $scope.config_body[key] = "Other";
              }
              $scope.cfg_values={};
            })
          }
        })

        angular.forEach($scope.config_body, function(value, key){
          //if($scope.metadata.meta[key].type == "select" && $scope.metadata.meta[key].valid_values.values.indexOf(value) == -1 ){
          if($scope.metadata.meta[key].type == "select" && $scope.metadata.meta[key].valid_values.type=="array"){
            if($filter('filter')($scope.metadata.meta[key].valid_values.values, value, true).length==0){
              $scope.others[key] = $scope.config_body[key];
              $scope.config_body[key] = "Other";
            }
          }
        })
      }
    });

  };

  $scope.loadConfig(); // Load a attribute which can be edited on UI
})
