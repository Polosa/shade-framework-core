<?php

/**
 * Shade
 *
 * @version 1.0.0
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
     */
    public function testExecute()
    {
        $app = new App();

        $response = new Response();
        $response->setCode(200);
        $response->setContent('test');

        $controllerDispatcherMock = $this->getMockBuilder('\Shade\ControllerDispatcher')
            ->setMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $controllerDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn($response);

        $app->setService(ServiceContainer::SERVICE_CONTROLLER_DISPATCHER, $controllerDispatcherMock);

        $response = $app->execute(new Request\Web($_SERVER));
        $this->assertInstanceOf('\Shade\Response', $response);
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('test', $response->getContent());
    }
}
