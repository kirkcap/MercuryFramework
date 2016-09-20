angular.module('mercuryFWConfigApp.controllers')

.controller('TesteListController', function($scope, $state, popupService, $window, CONFIG_API) {

  $scope.configs = CONFIG_API.query({ service: $state.current.svcname }); //fetch all attributes. Issues a GET to /api/attributes

  $scope.deleteConfig = function(cfg) { // Delete an attribute. Issues a DELETE to /api/attributes/:id

    if (popupService.showPopup('Really delete this?')) {
      $scope.config = CONFIG_API.get({ service: $state.current.svcname, cfg:cfg.key }, function() {
        $scope.config.$delete({ cfg:cfg.key }, function() {
          window.location.reload();
        });
      });
    }

  };

}).controller('TesteViewController', function($scope, $state, $stateParams, CONFIG_API, Utils) {
  $scope.config_key = {"value" : $stateParams.cfg };
  $scope.config = CONFIG_API.get({ service: $state.current.svcname, cfg: $scope.config_key.value }, function(){
    if(!$state.current.singleTypeParameters){
      $scope.config_body = $scope.config[$scope.config_key.value];
    }
  }); //Get a single attribute.Issues a GET to /api/attributes/:id

}).controller('TesteCreateController', function($scope, $state, $stateParams, CONFIG_API) {
  $scope.action = "new";
  $scope.config_key = {"key":""};
  $scope.config_body = {}; //Used only on complex parameters
  $scope.config = new CONFIG_API();  //create new attribute instance. Properties will be set via ng-model on UI

  $scope.addConfig = function() { //create a new attribute. Issues a POST to /api/attributes
    if(!$state.current.singleTypeParameters){
      $scope.config[$scope.config_key.value] = $scope.config_body; //Used only in complex parameters
    }
    $scope.config.$save({ service: $state.current.svcname, cfg: $scope.config_key.value }, function() {
      $state.go($state.current.sourceUrl);//'teste'); // on success go back to home i.e. attributes state.
    });
  };

}).controller('TesteEditController', function($scope, $state, $stateParams, CONFIG_API) {
  $scope.action = "edit";
  $scope.config_key = {"value" : $stateParams.cfg };
  $scope.updateConfig = function() { //Update the edited attribute. Issues a PUT to /api/attributes/:id
    if(!$state.current.singleTypeParameters){
      $scope.config[$scope.config_key.value] = $scope.config_body; //Used only in complex parameters
    }
    $scope.config.$update({ service: $state.current.svcname, cfg: $scope.config_key.value }, function() {
      $state.go($state.current.sourceUrl);//'teste'); // on success go back to home i.e. attributes state.
    });
  };

  $scope.loadConfig = function() { //Issues a GET request to /api/attributes/:id to get a attribute to update

    $scope.config = CONFIG_API.get({ service: $state.current.svcname, cfg: $scope.config_key.value }, function(){
      if(!$state.current.singleTypeParameters){
        $scope.config_body = $scope.config[$scope.config_key.value];//Used only in complex parameters
      }
    });

  };

  $scope.loadConfig(); // Load a attribute which can be edited on UI
})
