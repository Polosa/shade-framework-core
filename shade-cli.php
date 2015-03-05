#!/usr/bin/env php
<?php

/**
 * Shade CLI runner
 *
 * @package Shade
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

require_once 'vendor/autoload.php';
require_once 'config/bootstrap.php';
$app = new \Shade\App($config);
require_once 'config/di.php';
require_once 'config/routing.php';

$app->run();
