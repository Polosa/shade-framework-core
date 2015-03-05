<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Controller;

/**
 * Controller "Profiler"
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Profiler extends \Shade\Controller
{
    /**
     * Output profiler information
     *
     * @param string $startTime Application start time
     * @param bool   $showProfiler Is debug mode enabled
     *
     * @return \Shade\Response
     */
    public function outputAction($startTime, $showProfiler)
    {
        $data = ['showProfiler' => $showProfiler];
        if ($showProfiler) {
            $data = [
                'showProfiler' => true,
                'memory' => \Shade\Converter::formatBytes(memory_get_usage(true)),
                'memoryPeak' => \Shade\Converter::formatBytes(memory_get_peak_usage(true)),
                'execTime' => (microtime(true) - $startTime),
            ];
        }

        return $this->render('system/profiler.phtml', $data);
    }
}
