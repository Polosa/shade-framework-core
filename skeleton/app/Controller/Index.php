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
     *
     * @param string $appName Application name
     *
     * @return \Shade\Response
     */
    public function indexAction($appName)
    {
        return $this->render(
            array('index.phtml', 'main_layout.phtml', 'html_layout.phtml'),
            array('pageTitle' => $appName)
        );
    }
}
