<?php

/**
 * ShadeApp
 */

$classLoader = require_once '%ShadePath%/vendor/autoload.php';
$classLoader->addPsr4('ShadeApp\\', realpath('..'.DIRECTORY_SEPARATOR.'app'));
$app = new \ShadeApp\App('../config/app.ini');
$app->run();
