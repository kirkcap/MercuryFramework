<?php

/**
 * Mercury - The quickest PHP framework of the Earth to implement CRUD REST services
 *
 * @package  Mercury
 * @author   Wilson Santos <wilson.rsantos@gmail.com>
 */

/* Adjust the path below to the place in your server where you will host the
 * framework code(out of public_html folder, for security reasons :)
 */
require_once __DIR__.'/../mobilecars_mercury/backend/api.php';
use com\mercuryfw\api as api;
$api = new api\API;
$api->processApi();
?>
