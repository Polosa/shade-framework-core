<?php

/**
 * Shade
 *
 * @package Shade
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

$config = new \Shade\Config(require_once 'app_defaults.php');

// PHP error handling settings
if (isset($config->php->errorReportingLevel)) {
    error_reporting($config->php->errorReportingLevel->getValue());
}
if (isset($config->php->displayErrors)) {
    ini_set('display_errors', $config->php->displayErrors->getValue());
}
if (isset($config->php->logErrors)) {
    ini_set('log_errors', $config->php->logErrors->getValue());
}
if ($config->php->errorLogPath->getValue()) {
    ini_set('error_log', $config->php->errorLogPath->getValue());
}

// Application central logger
$logger = new \Shade\Logger('MAIN');
$logger->pushHandler(new \Monolog\Handler\NullHandler(\Monolog\Logger::DEBUG));
if ($config->debug->logging->logPath->getValue()) {
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($config->debug->logging->logPath->getValue(), \Monolog\Logger::DEBUG));
}
if (isset($config->debug->logging->logErrors)) {
    \Shade\Logger\ErrorHandler::register($logger);
}