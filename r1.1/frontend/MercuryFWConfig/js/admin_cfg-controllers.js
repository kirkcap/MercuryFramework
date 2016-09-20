angular.module('mercuryFWConfigApp.controllers', [])

.controller('AdminCfgListController', function($scope, $state, popupService, $window, AdminCfg) {

  $scope.configs = AdminCfg.query(); //fetch all attributes. Issues a GET to /api/attributes

  $scope.deleteConfig = function(cfg) { // Delete an attribute. Issues a DELETE to /api/attributes/:id
    if(cfg.key=='admin_frontend_allowed'){
      alert('Sorry, admin_frontend_allowed parameter can´t be deleted!');
    }else{
      if (popupService.showPopup('Really delete this?')) {
        $scope.config = AdminCfg.get({ cfg:cfg.key }, function() {
          $scope.config.$delete({ cfg:cfg.key }, function() {
            window.location.reload();
          });
        });
      }
    }
  };

}).controller('AdminCfgViewController', function($scope, $stateParams, AdminCfg) {
  $scope.config_key = {"value" : $stateParams.cfg };
  $scope.config = AdminCfg.get({ cfg: $scope.config_key.value }, function(){
    $scope.config_body = $scope.config[$scope.config_key.value];
  }); //Get a single attribute.Issues a GET to /api/attributes/:id

}).controller('AdminCfgCreateController', function($scope, $state, $stateParams, AdminCfg) {
  $scope.action = "new";
  $scope.config_key = {"key":""};
  //$scope.config_body = {}; //Used only on complex parameters
  $scope.config = new AdminCfg();  //create new attribute instance. Properties will be set via ng-model on UI

  $scope.addConfig = function() { //create a new attribute. Issues a POST to /api/attributes
    //$scope.config[$scope.config_key.value] = $scope.config_body; //Used only in complex parameters
    $scope.config.$save({ cfg: $scope.config_key.value }, function() {
      $state.go('admin_cfg'); // on success go back to home i.e. attributes state.
    });
  };

}).controller('AdminCfgEditController', function($scope, $state, $stateParams, AdminCfg) {
  $scope.action = "edit";
  $scope.config_key = {"value" : $stateParams.cfg };
  $scope.updateConfig = function() { //Update the edited attribute. Issues a PUT to /api/attributes/:id
    //$scope.config[$scope.config_key.value] = $scope.config_body; //Used only in complex parameters
    $scope.config.$update({ cfg: $scope.config_key.value }, function() {
      $state.go('admin_cfg'); // on success go back to home i.e. attributes state.
    });
  };

  $scope.loadConfig = function() { //Issues a GET request to /api/attributes/:id to get a attribute to update

    $scope.config = AdminCfg.get({ cfg: $scope.config_key.value }, function(){
      //$scope.config_body = $scope.config[$scope.config_key.value];//Used only in complex parameters
    });

  };

  $scope.loadConfig(); // Load a attribute which can be edited on UI
})
