#!/usr/bin/env php
<?php

/**
 * Shade CLI runner
 *
 * @package Shade
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

require_once 'vendor/autoload.php';
$app = new \Shade\App();
$app->run();
