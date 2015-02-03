<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Application test
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class AppTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test App execution
     *
     * @covers \Shade\App::execute
     */
    public function testExecute()
    {
        // Mock $_SERVER
        $server['REQUEST_URI'] = '/profiler/output/';

        $app = new App();
        $request = new Request\Web($server);
        $response = $app->execute($request);
        $this->assertInstanceOf('\Shade\Response', $response);
        $this->assertEquals(200, $response->getCode());
    }
}
