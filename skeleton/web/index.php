<?php

/**
 * ShadeApp
 */

/**
 * @var \Composer\Autoload\ClassLoader $classLoader
 */
$classLoader = require_once '%autoloadPath%';
$classLoader->addPsr4('ShadeApp\\', realpath('../app'), true);
$app = new \ShadeApp\App(require_once '../config/app.php');
require_once '../config/di.php';
require_once '../config/routing.php';
$app->run();
