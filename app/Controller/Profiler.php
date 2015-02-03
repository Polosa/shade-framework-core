<?php

/**
 * Shade
 *
 * @version 0.1
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
        $data = array('showProfiler' => $showProfiler);
        if ($showProfiler) {
            $data = array(
                'showProfiler' => true,
                'memory' => \Shade\Converter::formatBytes(memory_get_usage(true)),
                'memoryPeak' => \Shade\Converter::formatBytes(memory_get_peak_usage(true)),
                'execTime' => (microtime(true) - $startTime),
            );
        }

        return $this->render('system/profiler.phtml', $data);
    }
}
