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
* @category REST API Builder
* @package  Mercuryfw
* @author   Wilson Rodrigo dos Santos <wilson.santos@gmail.com>
* @license  http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
* @link     https://github.com/kirkcap/MercuryFramework
**/

/**
 * Mercury - The first PHP REST CRUD API Builder of the Earth
 */

/* Adjust the path below to the place in your server where you will host the
 * framework code(out of public_html folder, for security reasons :)
 */
require_once __DIR__.'/../{{your-project-name}}/backend/api.php';
use com\mercuryfw\api as api;
$api = new api\API;
$api->processApi();
?>
