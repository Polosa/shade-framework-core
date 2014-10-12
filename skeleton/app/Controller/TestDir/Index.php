<?php

/**
 * ShadeApp
 */

namespace ShadeApp\Controller\TestDir;

/**
 * Controller "Index"
 */
class Index extends \ShadeApp\Controller
{
    public function indexAction()
    {
        $data = array(
            'testVar' => 'testdir/index',
        );

        return $this->render(array('test.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }

    public function testAction($arg1, $arg2 = null)
    {
        $data = array(
            'pageTitle' => 'Shade Framework',
            'testVar' => 'testValue5',
            'vars' => array($arg1, $arg2),
        );

        return $this->render(array('test.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }
}
