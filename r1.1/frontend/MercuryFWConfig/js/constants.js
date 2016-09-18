angular.module('mercuryFWConfigApp.constants', [])

    .constant('BackendConfig', {
        url: 'http://localhost:8090/index_mc.php/'
    })

    .constant('Months', [
      {key:'01', text:'January'},
      {key:'02', text:'February'},
      {key:'03', text:'March'},
      {key:'04', text:'April'},
      {key:'05', text:'May'},
      {key:'06', text:'June'},
      {key:'07', text:'July'},
      {key:'08', text:'August'},
      {key:'09', text:'September'},
      {key:'10', text:'October'},
      {key:'11', text:'November'},
      {key:'12', text:'December'}
    ])

;
