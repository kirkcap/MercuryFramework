angular.module('mercuryFWConfigApp', ['ui.router', 'ngResource', 'mercuryFWConfigApp.constants', 'mercuryFWConfigApp.controllers', 'mercuryFWConfigApp.services']);

angular.module('mercuryFWConfigApp').config(function($stateProvider) {
  $stateProvider.state('home', { // state for showing all attributes
    url: '/home',
    templateUrl: 'partials/home.html' //,
    //controller: 'HomeController'
  })

/******
* Administration config
******* /
  .state('admin_cfg', { // state for showing all attributes
    url: '/admin_cfg',
    templateUrl: 'partials/admin_cfg/admin_cfg.html',
    controller: 'AdminCfgListController'
  }).state('viewAdminCfg', { //state for showing single attribute
    url: '/admin_cfg/:cfg/view',
    templateUrl: 'partials/admin_cfg/admin_cfg-view.html',
    controller: 'AdminCfgViewController'
  }).state('newAdminCfg', { //state for adding a new attribute
    url: '/admin_cfg/new',
    templateUrl: 'partials/admin_cfg/admin_cfg-add.html',
    controller: 'AdminCfgCreateController'
  }).state('editAdminCfg', { //state for updating a attribute
    url: '/admin_cfg/:cfg/edit',
    templateUrl: 'partials/admin_cfg/admin_cfg-edit.html',
    controller: 'AdminCfgEditController'
  })


/******
* Authorization config
******* /
  .state('auth_cfg', { // state for showing all attributes
    url: '/auth_cfg',
    templateUrl: 'partials/auth_cfg/auth_cfg.html',
    controller: 'AuthCfgListController'
  }).state('viewAuthCfg', { //state for showing single attribute
    url: '/auth_cfg/:cfg/view',
    templateUrl: 'partials/auth_cfg/auth_cfg-view.html',
    controller: 'AuthCfgViewController'
  }).state('newAuthCfg', { //state for adding a new attribute
    url: '/auth_cfg/new',
    templateUrl: 'partials/auth_cfg/auth_cfg-add.html',
    controller: 'AuthCfgCreateController'
  }).state('editAuthCfg', { //state for updating a attribute
    url: '/auth_cfg/:cfg/edit',
    templateUrl: 'partials/auth_cfg/auth_cfg-edit.html',
    controller: 'AuthCfgEditController'
  })

/******
* Database config
******* /
  .state('database', { // state for showing all attributes
    url: '/database',
    templateUrl: 'partials/database/databases.html',
    controller: 'DatabaseListController'
  }).state('viewDatabase', { //state for showing single attribute
    url: '/database/:cfg/view',
    templateUrl: 'partials/database/database-view.html',
    controller: 'DatabaseViewController'
  }).state('newDatabase', { //state for adding a new attribute
    url: '/database/new',
    templateUrl: 'partials/database/database-add.html',
    controller: 'DatabaseCreateController'
  }).state('editDatabase', { //state for updating a attribute
    url: '/database/:cfg/edit',
    templateUrl: 'partials/database/database-edit.html',
    controller: 'DatabaseEditController'
  })

/******
* Model config
******* /
  .state('model', { // state for showing all attributes
    url: '/model',
    templateUrl: 'partials/model/models.html',
    controller: 'ModelListController'
  }).state('viewModel', { //state for showing single attribute
    url: '/model/:cfg/view',
    templateUrl: 'partials/model/model-view.html',
    controller: 'ModelViewController'
  }).state('newModel', { //state for adding a new attribute
    url: '/model/new',
    templateUrl: 'partials/model/model-add.html',
    controller: 'ModelCreateController'
  }).state('editModel', { //state for updating a attribute
    url: '/model/:cfg/edit',
    templateUrl: 'partials/model/model-edit.html',
    controller: 'ModelEditController'
  })

/******
* Route config
******* /
  .state('route', { // state for showing all attributes
    url: '/route',
    templateUrl: 'partials/route/routes.html',
    controller: 'RouteListController'
  }).state('viewRoute', { //state for showing single attribute
    url: '/route/:cfg/view',
    templateUrl: 'partials/route/route-view.html',
    controller: 'RouteViewController'
  }).state('newRoute', { //state for adding a new attribute
    url: '/route/new',
    templateUrl: 'partials/route/route-add.html',
    controller: 'RouteCreateController'
  }).state('editRoute', { //state for updating a attribute
    url: '/route/:cfg/edit',
    templateUrl: 'partials/route/route-edit.html',
    controller: 'RouteEditController'
  })

/******
* Teste config
*******/
  .state('teste', { // state for showing all attributes
    url: '/teste',
    templateUrl: 'partials/teste/testes.html',
    controller: 'TesteListController'
  }).state('viewTeste', { //state for showing single attribute
    url: '/teste/:cfg/view',
    templateUrl: 'partials/teste/teste-view.html',
    controller: 'TesteViewController'
  }).state('newTeste', { //state for adding a new attribute
    url: '/teste/new',
    templateUrl: 'partials/teste/teste-add.html',
    controller: 'TesteCreateController'
  }).state('editTeste', { //state for updating a attribute
    url: '/teste/:cfg/edit',
    templateUrl: 'partials/teste/teste-edit.html',
    controller: 'TesteEditController'
  })



}).run(function($state) {
  $state.go('home'); //make a transition to attributes state when app starts
})

.directive('stringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$parsers.push(function(value) {
        return '' + value;
      });
      ngModel.$formatters.push(function(value) {
        return parseFloat(value, 10);
      });
    }
  };
});
