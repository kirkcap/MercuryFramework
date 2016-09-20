angular.module('mercuryFWConfigApp', ['ui.router', 'ngResource', 'mercuryFWConfigApp.constants', 'mercuryFWConfigApp.controllers', 'mercuryFWConfigApp.services']);

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
    controller: 'GenericConfigListController',
    cfgname: 'AdminConfig',
    singleTypeParameters: true,
    svcname: 'admin_cfg',
    sourceUrl: 'adminConfig'
  }).state('viewAdminConfig', { //state for showing single attribute
    url: '/admin_cfg/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewController',
    cfgname: 'AdminConfig',
    singleTypeParameters: true,
    svcname: 'admin_cfg',
    sourceUrl: 'adminConfig'
  }).state('newAdminConfig', { //state for adding a new attribute
    url: '/admin_cfg/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController',
    cfgname: 'AdminConfig',
    singleTypeParameters: true,
    svcname: 'admin_cfg',
    sourceUrl: 'adminConfig'
  }).state('editAdminConfig', { //state for updating a attribute
    url: '/admin_cfg/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigEditController',
    cfgname: 'AdminConfig',
    singleTypeParameters: true,
    svcname: 'admin_cfg',
    sourceUrl: 'adminConfig'
  })


/******
* Authorization config
*******/
  .state('authConfig', { // state for showing all attributes
    url: '/auth_cfg',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController',
    cfgname: 'AuthConfig',
    singleTypeParameters: true,
    svcname: 'auth_cfg',
    sourceUrl: 'authConfig'
  }).state('viewAuthConfig', { //state for showing single attribute
    url: '/auth_cfg/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewController',
    cfgname: 'AuthConfig',
    singleTypeParameters: true,
    svcname: 'auth_cfg',
    sourceUrl: 'authConfig'
  }).state('newAuthConfig', { //state for adding a new attribute
    url: '/auth_cfg/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController',
    cfgname: 'AuthConfig',
    singleTypeParameters: true,
    svcname: 'auth_cfg',
    sourceUrl: 'authConfig'
  }).state('editAuthConfig', { //state for updating a attribute
    url: '/auth_cfg/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigEditController',
    cfgname: 'AuthConfig',
    singleTypeParameters: true,
    svcname: 'auth_cfg',
    sourceUrl: 'authConfig'
  })

/******
* Files config
*******/
  .state('configFiles', { // state for showing all attributes
    url: '/files',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController',
    cfgname: 'ConfigFile',
    singleTypeParameters: true,
    svcname: 'config_files',
    sourceUrl: 'configFiles'
  }).state('viewConfigFile', { //state for showing single attribute
    url: '/files/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewController',
    cfgname: 'ConfigFile',
    singleTypeParameters: true,
    svcname: 'config_files',
    sourceUrl: 'configFiles'
  }).state('newConfigFile', { //state for adding a new attribute
    url: '/files/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController',
    cfgname: 'ConfigFile',
    singleTypeParameters: true,
    svcname: 'config_files',
    sourceUrl: 'configFiles'
  }).state('editConfigFile', { //state for updating a attribute
    url: '/files/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigEditController',
    cfgname: 'ConfigFile',
    singleTypeParameters: true,
    svcname: 'config_files',
    sourceUrl: 'configFiles'
  })


/******
* Database config
*******/
  .state('dbConfig', { // state for showing all attributes
    url: '/db_config',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController',
    cfgname: 'DbConfig',
    singleTypeParameters: false,
    svcname: 'databases',
    sourceUrl: 'dbConfig'
  }).state('viewDbConfig', { //state for showing single attribute
    url: '/db_config/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewController',
    cfgname: 'DbConfig',
    singleTypeParameters: false,
    svcname: 'databases',
    sourceUrl: 'dbConfig'
  }).state('newDbConfig', { //state for adding a new attribute
    url: '/db_config/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController',
    cfgname: 'DbConfig',
    singleTypeParameters: false,
    svcname: 'databases',
    sourceUrl: 'dbConfig',
    schema: {
      DB : "Fill the DB Name",
      DB_TYPE : "mysql",
      DB_SERVER : "eg: localhost",
      DB_USER : "Fill the DB UserName",
      DB_PASSWORD : "Fill the DB Password",
      PREFIX_DB : false,
      PREFIX_TB : false
    }
  }).state('editDbConfig', { //state for updating a attribute
    url: '/db_config/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigEditController',
    cfgname: 'DbConfig',
    singleTypeParameters: false,
    svcname: 'databases',
    sourceUrl: 'dbConfig'
  })


/******
* Model config
*******/
  .state('modelConfig', { // state for showing all attributes
    url: '/model_config',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController',
    cfgname: 'ModelConfig',
    singleTypeParameters: false,
    svcname: 'models',
    sourceUrl: 'modelConfig'
  }).state('viewModelConfig', { //state for showing single attribute
    url: '/model_config/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewController',
    cfgname: 'ModelConfig',
    singleTypeParameters: false,
    svcname: 'models',
    sourceUrl: 'modelConfig'
  }).state('newModelConfig', { //state for adding a new attribute
    url: '/model_config/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController',
    cfgname: 'ModelConfig',
    singleTypeParameters: false,
    svcname: 'models',
    sourceUrl: 'modelConfig'
  }).state('editModelConfig', { //state for updating a attribute
    url: '/model_config/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigEditController',
    cfgname: 'ModelConfig',
    singleTypeParameters: false,
    svcname: 'models',
    sourceUrl: 'modelConfig'
  })




/******
* Route config
*******/
.state('routeConfig', { // state for showing all attributes
  url: '/route_config',
  templateUrl: 'partials/generic_cfg/generic_cfg.html',
  controller: 'GenericConfigListController',
  cfgname: 'RouteConfig',
  singleTypeParameters: false,
  svcname: 'routes',
  sourceUrl: 'routeConfig'
}).state('viewRouteConfig', { //state for showing single attribute
  url: '/route_config/:cfg/view',
  templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
  controller: 'GenericConfigViewController',
  cfgname: 'RouteConfig',
  singleTypeParameters: false,
  svcname: 'routes',
  sourceUrl: 'routeConfig'
}).state('newRouteConfig', { //state for adding a new attribute
  url: '/route_config/new',
  templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
  controller: 'GenericConfigCreateController',
  cfgname: 'RouteConfig',
  singleTypeParameters: false,
  svcname: 'routes',
  sourceUrl: 'routeConfig',
  schema: {
    controller : "Controller Name(for generic, use: genericCRUDController)",
    method : "Method Name(for CRUD, use CRUD)" ,
    checkToken : false,
    model : "Model Name"
  }
}).state('editRouteConfig', { //state for updating a attribute
  url: '/route_config/:cfg/edit',
  templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
  controller: 'GenericConfigEditController',
  cfgname: 'RouteConfig',
  singleTypeParameters: false,
  svcname: 'routes',
  sourceUrl: 'routeConfig'
})

/******
* Teste config
*******/
  .state('teste', { // state for showing all attributes
    url: '/teste',
    templateUrl: 'partials/generic_cfg/generic_cfg.html',
    controller: 'GenericConfigListController',
    cfgname: 'Teste',
    singleTypeParameters: false,
    svcname: 'teste',
    sourceUrl: 'teste'
  }).state('viewTeste', { //state for showing single attribute
    url: '/teste/:cfg/view',
    templateUrl: 'partials/generic_cfg/generic_cfg-view.html',
    controller: 'GenericConfigViewController',
    cfgname: 'Teste',
    singleTypeParameters: false,
    svcname: 'teste',
    sourceUrl: 'teste'
  }).state('newTeste', { //state for adding a new attribute
    url: '/teste/new',
    templateUrl: 'partials/generic_cfg/generic_cfg-add.html',
    controller: 'GenericConfigCreateController',
    cfgname: 'Teste',
    singleTypeParameters: false,
    svcname: 'teste',
    sourceUrl: 'teste',
    schema:{
      campo1:"Value for campo1",
      campo2:"Value for campo2"
    }
  }).state('editTeste', { //state for updating a attribute
    url: '/teste/:cfg/edit',
    templateUrl: 'partials/generic_cfg/generic_cfg-edit.html',
    controller: 'GenericConfigEditController',
    cfgname: 'Teste',
    singleTypeParameters: false,
    svcname: 'teste',
    sourceUrl: 'teste'
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
