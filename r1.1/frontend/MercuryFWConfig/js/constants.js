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

    .constant('ConfigMetadata',{

      AdminConfig:{
        schema:{"":""},
        parameter_list:["admin_frontend_allowed"],
        meta:{
          param_key: {type: "select", placeholder: "Fill the parameter name, currently only admin_frontend_allowed is used"},
          admin_frontend_allowed: {type: "boolean", placeholder: "(If set to false, this frontend app will not work anymore)"}
        }
      },

      AuthConfig:{
        schema:{"":""},
        parameter_list:["TOKEN_VALIDITY","SECRET_SERVER_KEY"],
        meta:{
          param_key: {type: "select", placeholder: "Fill the parameter name"},
          TOKEN_VALIDITY: {type: "number", placeholder: "Fill the validity of the Authorization Token, in minutes"},
          SECRET_SERVER_KEY: {type: "string", placeholder: "Fill the secret key to Authorization Token encoding"}
        }
      },

      ConfigFile:{
        schema:{"":""},
        parameter_list:["config_files","admin_cfg","auth_cfg","databases","models","routes","teste"],
        meta:{
          param_key: {type: "string", placeholder: "Fill the file parameter name"},
          config_files: {type: "string", placeholder: "Fill the file name which contains the file names containing configurations"},
          admin_cfg: {type: "string", placeholder: "Fill the file name which contains the Administration Configuration"},
          auth_cfg: {type: "string", placeholder: "Fill the file name which contains the Authorization Configuration"},
          databases: {type: "string", placeholder: "Fill the file name which contains the Database(s) Configuration"},
          models: {type: "string", placeholder: "Fill the file name which contains the Models Configuration"},
          routes: {type: "string", placeholder: "Fill the file name which contains the Routes Configuration"},
        }
      },

      DbConfig:{
        schema:{
              DB : "",
              DB_TYPE : "",
              DB_SERVER : "",
              DB_USER : "",
              DB_PASSWORD : "",
              PREFIX_DB : "",
              PREFIX_TB : ""
            },
        meta:{
          param_key: {type: "string", placeholder: "Fill the DB Config Name, for the default to be used by the API, use 'default'"},
          DB : {type: "string", placeholder: "Fill the Database Name"},
          DB_TYPE : {type: "select", placeholder: "Fill the Database Type(currently support only for mysql)", valid_values:{type:"array", values:[{key:'mysql',value:'MySQL DB'}]}},
          DB_SERVER: {type: "string", placeholder: "Fill the Database Server"},
          DB_USER: {type: "string", placeholder: "Fill the Database User"},
          DB_PASSWORD: {type: "string", placeholder: "Fill the Database User Password"},
          PREFIX_DB: {type: "boolean", placeholder: "(Must SQL prefix Database Name?)"},
          PREFIX_TB: {type: "boolean", placeholder: "(Must SQL prefix Table Names?)"}
        }
      },

      RouteConfig:{
        schema:{
          controller : "",
          method : "" ,
          checkToken : false,
          model : ""
        },
        meta:{
          param_key: {type: "string", placeholder: "Fill the route name, if you have object + subobject, fill as object.subobject"},
          controller: {type: "select", placeholder: "Fill the Controller Name which will be process this route", valid_values:{type:"array", values:[{key: "genericCRUDController", value:"Generic Controller which deal with CRUD operations automatically"},{key: "genericAuthController",value: "Controller designed to deal with Authentication - Login and JWT Generation"}, {key: "configurationController", value:"Controller designed to deal with Configuration Files"}, {key:"Other", value:"Indicate the controller name:"}]}},
          method: {type: "select", placeholder: "Fill the Controller Method to be called by this route", valid_values:{type:"array",
            values:[{key:"CRUD", value: "Automatic process of all CRUD operations, according to HTTP Method used"},
                    {key:"index", value:"List all elements, HTTP GET"},
                    {key:"show", value:"Get a single element, HTTP GET"},
                    {key:"create", value:"Create new elements - HTTP POST"},
                    {key:"update", value:"Update elements, HTTP PUT"},
                    {key:"destroy", value: "delete elements, HTTP DELETE"},
                    {key: "login", value: "User/Password credentials check and JWT issuing"},
                    {key: "Other", value:"Indicate the method name:"}]}},
          checkToken: {type: "boolean", placeholder: "(Authorizatin Token must be checked for this route?)"},
          model: {type: "select", placeholder: "Fill the Model Name to be used", valid_values:{type:"service_call", service: "models"}}
        }
      },

      ModelConfig:{
        schema:{
            entity_id   : "",
            entity_name : "",
            tb_name     : "",
            tb_key      : [],
            tb_columns  : {field  : {key : false , show : false , insert : false, update : false, bind_type : "s", order:"", default:{type:"", value:"", fill_on_insert: false, fill_on_update: false}}
                            }
        },
        meta:{
          param_key: {type: "string", placeholder: "Fill the Model name, which will be used to configure Route"},
          dbCfgName: {type: "select", placeholder: "DB Configuration from where this model must be readed", valid_values:{type:"service_call", service:"databases"}},
          entity_id: {type: "string", placeholder: "Fill the Entity ID which is represented by this Model"},
          entity_name: {type: "string", placeholder: "Fill the Entity Name which is represented by this Model"},
          tb_name: {type: "string", placeholder: "Fill the Table Name which is represented by this Model"},
          tb_key: {type: "array", placeholder: "Fill the list of field(s) which compose the key of the Table"},
          tb_columns: {type: "object", placeholder: "Fill the Field(s) and attributes of each one",
            field: {type: "object", placeholder: "Fill the Field Name",
              key: {type: "boolean", placeholder: "Is a Key Field?"},
              show:{type: "boolean", placeholder: "Must be shown?"},
              insert: {type: "boolean", placeholder: "Can be inserted by User?"},
              update: {type: "boolean", placeholder: "Can be updated by User?"},
              bind_type: {type:"select",
                          placeholder: "Indicate the bind_type",
                          valid_values:{type: "array", values:[{key:"i", value:"Integer"}, {key:"d", value:"decimal"}, {key: "s", value: "string/date/time"}]}},
              order:{type:"select", placeholder: "Indicate the sort order if needed",
              valid_values:{type: "array", values:[{key: "asc", value:"Ascending"},{key: "desc", value:"Descending"}]}},
              default:{type:"object", placeholder: "Indicate default value if needed",
                type:"select", placeholder: "Indicate the type of default", valid_values:{type: "array", values:[{key: "function", value:"Function"},{key: "Token", value:"Authorization Token"},{key:"Other", value:"Indicate the value:"}]},
                value:"string", placeholder: "Indicate the value",
                fill_on_insert: "boolean", placeholder: "Must be filled on insert?",
                fill_on_update: "boolean", placeholder: "Must be filled on update?"
              }
            }
          }
        }
      }


    })

;
