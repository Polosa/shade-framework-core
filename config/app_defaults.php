<?php

/**
 * Shade
 *
 * @package Shade
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

/* Default application configuration */

return [
    'debug' => [
        'profilerEnabled' => false,
        'logging' => [
            'logPath' => null,
            'logErrors' => null,
        ]
    ],
    'php' => [
        'errorReportingLevel' => null,
        'displayErrors' => null,
        'logErrors' => null,
        'errorLogPath' => null,
    ],
];