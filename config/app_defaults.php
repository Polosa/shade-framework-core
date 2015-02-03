<?php

/**
 * Shade
 *
 * @package Shade
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

/* Default application configuration */

return array(
    'debug' => array(
        'error_reporting_level' => E_ALL | E_STRICT,
        'display_errors' => 'Off',
        'log_errors' => 'On',
        'error_log_path' => null,
        'debug_helpers_enabled' => false,
        'profiler_enabled' => false
    )
);