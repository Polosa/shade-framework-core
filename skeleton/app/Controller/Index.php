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
        return $this->render(
            array('index.phtml', 'main_layout.phtml', 'html_layout.phtml'),
            array('pageTitle' => $this->serviceProvider()->getApp()->getAppNamespace())
        );
    }
}
