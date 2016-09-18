angular.module('mercuryFWConfigApp.services', [])
.factory('Teste', function($resource, BackendConfig) {
  return $resource(BackendConfig.url + 'teste/:cfg', { cfg: '@cfg' }, {
    update: {method:'PUT', params: { cfg: '@cfg' }},
    get:    {method:'GET', params: { cfg: '@cfg' }},
    save:   {method:'POST', params: { cfg: '@cfg' }},
    query:  {method:'GET', isArray:false},
    remove: {method:'DELETE', params: { cfg: '@cfg' }},
    delete: {method:'DELETE', params: { cfg: '@cfg' }}
  });
})



.service('popupService',function($window){
    this.showPopup=function(message){
        return $window.confirm(message);
    }
});
