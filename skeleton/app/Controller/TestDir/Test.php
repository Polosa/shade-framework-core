<?php

/**
 * ShadeApp
 */

namespace ShadeApp\Controller\TestDir;

/**
 * Controller "Index"
 */
class Test extends \ShadeApp\Controller
{
    public function indexAction()
    {
        $data = array(
            'testVar' => 'testValue6',
        );

        return $this->render(array('test.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }

    public function testAction($arg1, $arg2 = null)
    {
        $data = array(
            'pageTitle' => 'Shade Framework',
            'testVar' => 'testValue7',
            'vars' => array($arg1, $arg2),
        );

        return $this->render(array('test.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }
}
