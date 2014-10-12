<?php

/**
 * ShadeApp
 */

namespace ShadeApp\Controller;

/**
 * Controller "Index"
 */
class Index extends \ShadeApp\Controller
{
    /**
	 * Main page
	 */
    public function indexAction()
    {
        $data = array(
            'testVar' => 'testValue',
        );

        return $this->render(array('index.phtml', 'main_layout.phtml', 'html_layout.phtml'), $data);
    }
}
