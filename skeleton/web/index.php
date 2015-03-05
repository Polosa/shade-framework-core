<?php

/**
 * ShadeApp
 */

require_once __DIR__.'/../config/bootstrap.php';
$app = new \ShadeApp\App($config);
require_once __DIR__.'/../config/di.php';
require_once __DIR__.'/../config/routing.php';
$app->run();
