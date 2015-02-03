<?php

/**
 * ShadeApp
 */

$classLoader = require_once '%autoloadPath%';
$classLoader->addPsr4('ShadeApp\\', realpath('..'.DIRECTORY_SEPARATOR.'app'));
$app = new \ShadeApp\App('../config/app.ini');
require_once '../config/di.php';
require_once '../config/routing.php';
$app->run();
