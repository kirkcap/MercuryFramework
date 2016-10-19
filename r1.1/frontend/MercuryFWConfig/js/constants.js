/*
Copyright 2016 Wilson Rodrigo dos Santos - wilson.santos@gmail.com

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/
/**
* Angular JS
*
* @category Constants
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

angular.module('mercuryFWConfigApp.constants', [])

    .constant('BackendConfig', {
        url: 'http://localhost:{{port}}/index.php/'
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
        configEntity: "Administration",
        singleTypeParameters: true,
        svcname:   'admin_cfg',
        sourceUrl: 'adminConfig',
        schema:{"":""},
        parameter_list:["admin_frontend_allowed","cors_allow_origin","log_option","log_saver_class","log_saver_destination","exception_return","exception_friendly_message"],
        meta:{
          param_key:                   {fldtype: "select", placeholder: "Fill the parameter name, currently only admin_frontend_allowed is used"},
          admin_frontend_allowed:      {fldtype: "boolean", placeholder: "(If set to false, this frontend app will not work anymore)"},
          cors_allow_origin:           {fldtype: "string", placeholder: "Indicate the allowed domain for CORS"},
          log_option:                  {fldtype: "select", placeholder: "Select the type of log processing", valid_values:{type:"array", values:[{key:'all',value:'Logs Everything'}, {key:'info',value:'Logs Info, Warning and Error type messages'}, {key:'warning',value:'Logs Warning and Error Type Messages'}, {key:'error',value:'Logs Error type messages'}]} },
          log_saver_class:             {fldtype: "select", placeholder: "Select the Logger Class", valid_values:{type:"array", values:[{key:'FileLogSaver',value:'Save log to file'}]} },
          log_saver_destination:       {fldtype: "string", placeholder: "For the FileLogSaver, the filename prefix, for other, only God knows..."},
          exception_return:            {fldtype: "select", placeholder: "Select the way to deal with exceptions", valid_values:{type:"array", values:[{key:'exception_data',value:'Return Exception Details in REST Call'},{key:'default_message',value:'Return friendly message in REST Call'}]}},
          exception_friendly_message:  {fldtype: "string", placeholder: "Fill a friendly message to be returned when a exception occurs"}
        }
      },

      AuthConfig:{
        configEntity: "Authorization Token",
        singleTypeParameters: true,
        svcname:   'auth_cfg',
        sourceUrl: 'authConfig',
        schema:{"":""},
        parameter_list:["TOKEN_VALIDITY","SECRET_SERVER_KEY"],
        meta:{
          param_key: {fldtype: "select", placeholder: "Fill the parameter name"},
          TOKEN_VALIDITY: {fldtype: "number", placeholder: "Fill the validity of the Authorization Token, in minutes"},
          SECRET_SERVER_KEY: {fldtype: "string", placeholder: "Fill the secret key to Authorization Token encoding"}
        }
      },

      ConfigFile:{
        configEntity: "Config File",
        singleTypeParameters: true,
        svcname:   'config_files',
        sourceUrl: 'configFiles',
        schema:{"":""},
        parameter_list:["config_files","admin_cfg","auth_cfg","databases","models","routes","teste"],
        meta:{
          param_key: {fldtype: "string", placeholder: "Fill the file parameter name"},
          config_files: {fldtype: "string", placeholder: "Fill the file name which contains the file names containing configurations"},
          admin_cfg: {fldtype: "string", placeholder: "Fill the file name which contains the Administration Configuration"},
          auth_cfg: {fldtype: "string", placeholder: "Fill the file name which contains the Authorization Configuration"},
          databases: {fldtype: "string", placeholder: "Fill the file name which contains the Database(s) Configuration"},
          models: {fldtype: "string", placeholder: "Fill the file name which contains the Models Configuration"},
          routes: {fldtype: "string", placeholder: "Fill the file name which contains the Routes Configuration"},
        }
      },

      DbConfig:{
        configEntity: "Database Connection",
        singleTypeParameters: false,
        svcname:   'databases',
        sourceUrl: 'dbConfig',
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
          param_key: {fldtype: "string", placeholder: "Fill the DB Config Name, for the default to be used by the API, use 'default'"},
          DB : {fldtype: "string", placeholder: "Fill the Database Name"},
          DB_TYPE : {fldtype: "select", placeholder: "Fill the Database Type", valid_values:{type:"array", values:[{key:'mysql',value:'MySQL DB - MySQLi Driver'},{key:'mysql_PDO',value:'MySQL DB - PDO Driver'},{key:'pgsql_PDO',value:'PosgGreSQL DB - PDO Driver'}]}},
          DB_SERVER: {fldtype: "string", placeholder: "Fill the Database Server"},
          DB_USER: {fldtype: "string", placeholder: "Fill the Database User"},
          DB_PASSWORD: {fldtype: "string", placeholder: "Fill the Database User Password"},
          PREFIX_DB: {fldtype: "boolean", placeholder: "(Must SQL prefix Database Name?)"},
          PREFIX_TB: {fldtype: "boolean", placeholder: "(Must SQL prefix Table Names?)"}
        }
      },

      ModelConfig:{
        configEntity: "Model",
        singleTypeParameters: false,
        svcname:   'models',
        sourceUrl: 'modelConfig',
        schema:{
            dbCfgName     : "",
            entity_id     : "",
            entity_name   : "",
            tb_name       : "",
            tb_key        : [],
            max_recs_sel  : 100,
            isAuthModel   : false,
            login_field   : "",
            pwd_field     : "",
            tb_columns    : {}
        },
        tb_columns_schema: { },
        tb_columns_field_schema: {
                                   label: "", dbtype: "", key : false , show : false , insert : false, update : false, bind_type : "", order:"", default:{}
                                 },
        tb_columns_field_default_schema: {type:"", value:"", fill_on_insert: false, fill_on_update: false},
        meta:{
          param_key   : {fldtype: "string", placeholder: "Fill the Model name, which will be used to configure Route"},
          dbCfgName   : {fldtype: "select", placeholder: "DB Configuration from where this model must be readed", valid_values:{type:"service_call", service:"databases"}},
          entity_id   : {fldtype: "string", placeholder: "Fill the Entity ID which is represented by this Model"},
          entity_name : {fldtype: "string", placeholder: "Fill the Entity Name which is represented by this Model"},
          tb_name     : {fldtype: "string", placeholder: "Fill the Table Name which is represented by this Model"},
          tb_key      : {fldtype: "array", placeholder: "Fill the list of field(s) which compose the key of the Table"},
          max_recs_sel: {fldtype: "number", placeholder: "Fill the max records number to be returned by a selection(pagination length)"},
          isAuthModel : {fldtype: "boolean", placeholder: "Is this model used for authentication?"},
          login_field : {fldtype: "string", placeholder: "Fill the Field which contains the user login(valid only for the model used for authentication)", depends_on: {field:"isAuthModel", values:[true]} },
          pwd_field   : {fldtype: "string", placeholder: "Fill the Field which contains the user password(valid only for the model used for authentication)", depends_on: {field:"isAuthModel", values:[true]} },
          tb_columns  : {fldtype: "object", placeholder: "Fill the Field(s) and attributes of each one",
            useDialog: true,
            dialogForm: "cfgDialogTbColumnsEdit.html",
            dialogController: "CfgTbColumnsDialogEditController as TbColumnsVm",
            dialogClass: "ngdialog-custom-tb_columns",
            field_groups: {0: 'Basic Field Data Configuration',
                           1: 'Show/Insert/Update Configuration',
                           2: 'Default Data Configuration'},
            structure: {
              field: {fldtype: "object", placeholder: "Fill the Field Name",
                structure:{
                  key: {fldtype: "boolean", group:0, placeholder: "Is a Key Field?", noEdit: true, noShow: false},
                  label: {fldtype: "string", group:0, placeholder: "Fill a Label for the Field", noShow: false},
                  dbtype: {fldtype: "string", group:0, placeholder: "Database type", noEdit: true, noShow: false},
                  show:{fldtype: "boolean", group:1, placeholder: "Can be shown/returned in query?", noShow: false},
                  insert: {fldtype: "boolean", group:1, placeholder: "Can be inserted by User?", noShow: false},
                  update: {fldtype: "boolean", group:1, placeholder: "Can be updated by User?", noShow: false},
                  bind_type: {fldtype:"select",
                              group:0,
                              placeholder: "Indicate the bind_type",
                              noShow: false,
                              valid_values:{type: "array", values:[{key:"i", value:"Integer"}, {key:"d", value:"decimal"}, {key: "s", value: "string/date/time"}]}},
                  order:{fldtype:"select", group:0, placeholder: "Indicate the sort order if needed", noShow: false, valid_values:{type: "array", values:[{key: "", value:""},{key: "asc", value:"Ascending"},{key: "desc", value:"Descending"}]}},
                  default:{fldtype:"object", placeholder: "Indicate default value if needed", noShow: true,
                    useDialog: true,
                    dialogForm: "cfgDialogTbColumnsFieldDefaultEdit.html",
                    dialogController: "CfgTbColumnsFieldDefaultDialogEditController as FieldDefaultVm",
                    dialogClass: "ngdialog-custom-tb_columns-field-default",
                    structure:{
                      type: {fldtype:"select", group:2, placeholder: "Indicate the type of default", noShow: false, valid_values:{type: "array", values:[{key: "", value:""},{key: "function", value:"PHP Function"},{key: "token_id", value:"Authorization Token"}]}},
                      value:{fldtype:"select", group:2, placeholder: "Indicate the complementing value", noShow: false, depends_on: {field:"type", values:["function"]}, valid_values:{type: "array", values:[{key: "", value:""},{key: "current_timestamp", value:"Gets Current Timestamp"}]}},
                      fill_on_insert: {fldtype:"boolean", group:2, placeholder: "Must be filled on insert?", noShow: false},
                      fill_on_update: {fldtype:"boolean", group:2, placeholder: "Must be filled on update?", noShow: false}
                    }
                  }
                }
              }
            }
          }
        }
      },

      RouteConfig:{
        configEntity: "Route",
        singleTypeParameters: false,
        svcname:   'routes',
        sourceUrl: 'routeConfig',
        schema:{
          controller : "",
          method : "" ,
          http_method: "*",
          checkToken : false,
          model : ""
        },
        meta:{
          param_key: {fldtype: "string", placeholder: "Fill the route name, if you have object + subobject, fill as object.subobject"},
          controller: {fldtype: "select", placeholder: "Fill the Controller Name which will be process this route", valid_values:{type:"array", values:[{key: "genericCRUDController", value:"Generic Controller which deal with CRUD operations automatically"},{key: "genericAuthController",value: "Controller designed to deal with Authentication - Login and JWT Generation"}, {key: "configurationController", value:"Controller designed to deal with Configuration Files"}, {key:"Other", value:"Indicate the controller name:"}]}},
          method: {fldtype: "select", placeholder: "Fill the Controller Method to be called by this route", valid_values:{type:"array",
            values:[{key: "CRUD", value: "Automatic process of all CRUD operations, according to HTTP Method used"},
                    {key: "index", value:"List all elements, HTTP GET"},
                    {key: "show", value:"Get a single element, HTTP GET"},
                    {key: "create", value:"Create new elements - HTTP POST"},
                    {key: "update", value:"Update elements, HTTP PUT"},
                    {key: "destroy", value: "delete elements, HTTP DELETE"},
                    {key: "login", value: "User/Password credentials check and JWT issuing, HTTP POST"},
                    {key: "Other", value:"Indicate the method name:"}]}},
          http_method: {fldtype: "select", placeholder: "Fill the HTTP Method corresponding to the method configured", valid_values:{type:"array",
            values:[{key: "GET"   , value: "HTTP GET"},
                    {key: "POST"  , value: "HTTP POST"},
                    {key: "PUT"   , value: "HTTP PUT"},
                    {key: "DELETE", value: "HTTP DELETE"},
                    {key: "*"     , value: "GET,POST,PUT,DELETE"}]},
            depends_on: {field:"method", values:["Other"]}},
          checkToken: {fldtype: "boolean", placeholder: "(Authorizatin Token must be checked for this route?)"},
          model: {fldtype: "select", placeholder: "Fill the Model Name to be used", valid_values:{type:"service_call", service: "models"}}
        }
      }


    })

    .constant('DBTypes_X_BindTypes',{

      "char":"s",
      "varchar":"s",
      "tinytext":"s",
      "text":"s",
      "blob":"s",
      "mediumtext":"s",
      "mediumblob":"s",
      "longtext":"s",
      "longblob":"s",

      "tinyint":"i",
      "smallint":"i",
      "mediumint":"i",
      "int":"i",
      "bigint":"i",
      "float":"d",
      "double":"d",
      "decimal":"d",

      "date":"s",
      "datetime":"s",
      "timestamp":"s",
      "time":"s",
      "year":"s",

      "default":"s"

    })

;
