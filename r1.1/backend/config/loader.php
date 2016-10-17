<?php
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
* PHP version 5
*
* @category Loader
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
*/

require_once __ROOT__."/backend/api/application/helpers/jwt_helper.php";
require_once __ROOT__."/backend/api/application/helpers/auth_helper.php";
require_once __ROOT__."/backend/api/application/helpers/models_helper.php";
require_once __ROOT__."/backend/api/router/router.php";
require_once __ROOT__."/backend/api/controllers/configurationController.php";
require_once __ROOT__."/backend/api/controllers/dbMetadataController.php";
require_once __ROOT__."/backend/api/controllers/ControllerFactory.php";
require_once __ROOT__."/backend/api/controllers/abstractController.php";
require_once __ROOT__."/backend/api/controllers/genericCRUDController.php";
require_once __ROOT__."/backend/api/controllers/genericAuthController.php";
require_once __ROOT__."/backend/api/application/helpers/db_helper.php";

/*
* Add here any additional require for controllers, for example
*/

 ?>
