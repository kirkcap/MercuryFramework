angular.module('mercuryFWConfigApp.services', [])

.factory('CONFIG_API', function($resource, BackendConfig) {
  return $resource(BackendConfig.url + ':service/:cfg', { service: '@service', cfg: '@cfg' }, {
    update: {method:'PUT', params: { service: '@service', cfg: '@cfg' }},
    get:    {method:'GET', params: { service: '@service', cfg: '@cfg' }},
    save:   {method:'POST', params: { service: '@service', cfg: '@cfg' }},
    query:  {method:'GET', isArray:false, params: { service: '@service' }},
    remove: {method:'DELETE', params: { service: '@service', cfg: '@cfg' }},
    delete: {method:'DELETE', params: { service: '@service', cfg: '@cfg' }}
  });
})

.factory('DBTableStructure', function($resource, BackendConfig) {
  return $resource(BackendConfig.url + 'dbMetadata/:db_name/tbMetadata/:tb_name', { db_name: '@db_name', tb_name: '@tb_name' }, {
    get:    {method:'GET', isArray:true, params: { db_name: '@db_name', tb_name: '@tb_name' }}
    //query:  {method:'GET', isArray:false, params: { service: '@service' }},
  });
})


.factory('Utils', function(){
  return {
    isSingleType: function(data){
      $data_type = ucase( typeof data );
      switch($data_type){
        case "STRING":
          return true;
          break;
        case "NUMBER":
          return true;
          break;
        case "BOOLEAN":
          return true;
          break;
        case "OBJECT":
          return false;
          break;
        case "ARRAY":
          return false;
          break;
      }
    }
  }
})

.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});
