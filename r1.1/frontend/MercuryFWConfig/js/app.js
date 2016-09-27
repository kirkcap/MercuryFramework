angular.module('mercuryFWConfigApp', ['ui.router', 'ngDialog', 'ngResource', 'mercuryFWConfigApp.constants', 'mercuryFWConfigApp.controllers', 'mercuryFWConfigApp.services']);

angular.module('mercuryFWConfigApp').config(function($stateProvider) {
  $stateProvider.state('home', { // state for showing all attributes
    url: '/home',
    templateUrl: 'partials/home.html' //,
    //controller: 'HomeController'
  })

/******
* Administration config
*******/
  .state('adminConfig', { // state for showing all attributes
    url: '/admin_cfg',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController as vm',
    cfgname: 'AdminConfig'
  }).state('viewAdminConfig', { //state for showing single attribute
    url: '/admin_cfg/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'AdminConfig',
    action: 'view',
  }).state('newAdminConfig', { //state for adding a new attribute
    url: '/admin_cfg/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController as vm',
    cfgname: 'AdminConfig',
  }).state('editAdminConfig', { //state for updating a attribute
    url: '/admin_cfg/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'AdminConfig',
    action: 'edit',
  })


/******
* Authorization config
*******/
  .state('authConfig', { // state for showing all attributes
    url: '/auth_cfg',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController as vm',
    cfgname: 'AuthConfig',
  }).state('viewAuthConfig', { //state for showing single attribute
    url: '/auth_cfg/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'AuthConfig',
    action: 'view',
  }).state('newAuthConfig', { //state for adding a new attribute
    url: '/auth_cfg/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController as vm',
    cfgname: 'AuthConfig',
  }).state('editAuthConfig', { //state for updating a attribute
    url: '/auth_cfg/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'AuthConfig',
    action: 'edit',
  })

/******
* Files config
*******/
  .state('configFiles', { // state for showing all attributes
    url: '/files',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController as vm',
    cfgname: 'ConfigFile',
  }).state('viewConfigFile', { //state for showing single attribute
    url: '/files/:cfg/view', //view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'ConfigFile',
    action: 'view',
  }).state('newConfigFile', { //state for adding a new attribute
    url: '/files/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController as vm',
    cfgname: 'ConfigFile',
  }).state('editConfigFile', { //state for updating a attribute
    url: '/files/:cfg/edit', //edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'ConfigFile',
    action: 'edit',
  })


/******
* Database config
*******/
  .state('dbConfig', { // state for showing all attributes
    url: '/db_config',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController as vm',
    cfgname: 'DbConfig',
  }).state('viewDbConfig', { //state for showing single attribute
    url: '/db_config/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'DbConfig',
    action: 'view',
  }).state('newDbConfig', { //state for adding a new attribute
    url: '/db_config/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController as vm',
    cfgname: 'DbConfig',
  }).state('editDbConfig', { //state for updating a attribute
    url: '/db_config/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'DbConfig',
    action: 'edit',
  })


/******
* Model config
*******/
  .state('modelConfig', { // state for showing all attributes
    url: '/model_config',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController as vm',
    cfgname: 'ModelConfig',
  }).state('viewModelConfig', { //state for showing single attribute
    url: '/model_config/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'ModelConfig',
    action: 'view',
  }).state('newModelConfig', { //state for adding a new attribute
    url: '/model_config/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'ModelConfigCreateController as vm',
    cfgname: 'ModelConfig',
  }).state('editModelConfig', { //state for updating a attribute
    url: '/model_config/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'ModelConfigViewEditController as vm',
    cfgname: 'ModelConfig',
    action: 'edit'
  })




/******
* Route config
*******/
  .state('routeConfig', { // state for showing all attributes
    url: '/route_config',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController as vm',
    cfgname: 'RouteConfig',
  }).state('viewRouteConfig', { //state for showing single attribute
    url: '/route_config/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'RouteConfig',
    action: 'view',
  }).state('newRouteConfig', { //state for adding a new attribute
    url: '/route_config/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController as vm',
    cfgname: 'RouteConfig',
  }).state('editRouteConfig', { //state for updating a attribute
    url: '/route_config/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigViewEditController as vm',
    cfgname: 'RouteConfig',
    action: 'edit',
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
