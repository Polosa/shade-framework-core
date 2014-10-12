<?php

/**
 * ShadeApp
 */

namespace ShadeApp\Controller;

/**
 * Controller "Test2"
 */
class Test2 extends \ShadeApp\Controller
{
    public function testAction($arg1, $arg2 = null)
    {
        $data = array(
            'pageTitle' => 'Shade Framework',
            'testVar' => 'testValue',
            'vars' => array($arg1, $arg2),
        );

        return $this->render(array('test.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }
}
