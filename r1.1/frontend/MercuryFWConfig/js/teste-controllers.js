angular.module('mercuryFWConfigApp.controllers', [])

.controller('TesteListController', function($scope, $state, popupService, $window, Teste) {

  $scope.testes = Teste.query(); //fetch all attributes. Issues a GET to /api/attributes

  $scope.deleteTeste = function(cfg) { // Delete an attribute. Issues a DELETE to /api/attributes/:id

    if (popupService.showPopup('Really delete this?')) {
      $scope.teste = Teste.get({ cfg:cfg.key }, function() {
        $scope.teste.$delete({ cfg:cfg.key }, function() {
          //$state.go('attributes'); // on success go back to home i.e. attributes state.
          window.location.reload();
        });
      });
      /*teste.$delete(function() {
        //$state.go('attributes'); // on success go back to home i.e. attributes state.
        window.location.reload();
      });*/
    }
  };

}).controller('TesteViewController', function($scope, $stateParams, Teste) {
  $scope.config = {"key" : $stateParams.cfg };
  $scope.teste = Teste.get({ cfg: $scope.config.key }, function(){
    $scope.teste_data = $scope.teste[$scope.config.key];
  }); //Get a single attribute.Issues a GET to /api/attributes/:id

}).controller('TesteCreateController', function($scope, $state, $stateParams, Teste) {
  $scope.action = "new";
  $scope.config = {"key":""};
  $scope.teste_data = {};
  $scope.teste = new Teste();  //create new attribute instance. Properties will be set via ng-model on UI

  $scope.addTeste = function() { //create a new attribute. Issues a POST to /api/attributes
    $scope.teste[$scope.config.key] = $scope.teste_data;
    $scope.teste.$save({ cfg: $scope.config.key }, function() {
      $state.go('teste'); // on success go back to home i.e. attributes state.
    });
  };

}).controller('TesteEditController', function($scope, $state, $stateParams, Teste) {
  $scope.action = "edit";
  $scope.config = {"key" : $stateParams.cfg };
  $scope.updateTeste = function() { //Update the edited attribute. Issues a PUT to /api/attributes/:id
    $scope.teste[$scope.config.key] = $scope.teste_data;
    $scope.teste.$update({ cfg: $scope.config.key }, function() {
      $state.go('teste'); // on success go back to home i.e. attributes state.
    });
  };

  $scope.loadTeste = function() { //Issues a GET request to /api/attributes/:id to get a attribute to update

    $scope.teste = Teste.get({ cfg: $scope.config.key }, function(){
      $scope.teste_data = $scope.teste[$scope.config.key];
    });

  };

  $scope.loadTeste(); // Load a attribute which can be edited on UI
})
