<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Profiler
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Profiler
{
    const BENCHMARK_ITERATIONS_NUMBER = 100;

    /**
     * Do benchmarking
     *
     * @param callable $code             Code snippet for benchmarking
     * @param int      $iterationsNumber Number of iterations
     *
     * @return float
     */
    public function benchmark(callable $code, $iterationsNumber = self::BENCHMARK_ITERATIONS_NUMBER)
    {
        $startTime = microtime(true);
        for ($i = 1; $i < $iterationsNumber; $i++) {
            $code();
        }
        $endTime = microtime(true);

        return $endTime - $startTime;
    }
}
