<?php

/**
 * ShadeApp
 */

namespace ShadeApp\Controller;

/**
 * Controller "Test"
 */
class Test extends \ShadeApp\Controller
{
    public function indexAction()
    {
        $data = array(
            'testVar' => 'testValue2',
        );

        return $this->render(array('test.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }

    public function testAction($arg1, $arg2 = null)
    {
        $data = array(
            'pageTitle' => 'Shade Framework',
            'testVar' => 'testValue3',
            'vars' => array($arg1, $arg2),
        );

        return $this->render(array('test.phtml'), $data);
    }
}
