<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\View;

/**
 * View "Replace" Test
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ReplaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * View "Replace"
     *
     * @var \Shade\View\Replace
     */
    protected $view;

    /**
     * Setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->view = new Replace();
    }

    /**
     * Single template rendering test
     */
    public function testSingleTemplateRendering()
    {
        // single template as string
        $result = $this->view->render(
            __DIR__."/ReplaceTest/Templates/test_template.txt",
            array('%placeholder1%' => 'value1', '%placeholder2%' => 'value2')
        );
        $this->assertStringEqualsFile(__DIR__."/ReplaceTest/ExpectedResult/singleTemplateRendering.txt", $result);

        // single template as array
        $result = $this->view->render(
            array(__DIR__."/ReplaceTest/Templates/test_template.txt"),
            array('%placeholder1%' => 'value1', '%placeholder2%' => 'value2')
        );
        $this->assertStringEqualsFile(__DIR__."/ReplaceTest/ExpectedResult/singleTemplateRendering.txt", $result);
    }

    /**
     * Layouts rendering test
     */
    public function testLayoutsRenderingDefaultPlaceholder()
    {
        $result = $this->view->render(
            array(
                __DIR__."/ReplaceTest/Templates/test_template.txt",
                __DIR__."/ReplaceTest/Templates/test_layout1.txt",
                __DIR__."/ReplaceTest/Templates/test_layout2.txt",
            ),
            array('%placeholder1%' => 'value1', '%placeholder2%' => 'value2')
        );

        $this->assertStringEqualsFile(
            __DIR__."/ReplaceTest/ExpectedResult/layoutsRenderingDefaultPlaceholder.txt",
            $result
        );
    }

    /**
     * Layouts rendering test (custom placeholder)
     */
    public function testLayoutsRenderingCustomPlaceholder()
    {
        $view = clone $this->view;
        $view->setLayoutContentPlaceholder('%custom_placeholder%');

        $result = $view->render(
            array(
                __DIR__."/ReplaceTest/Templates/test_template.txt",
                __DIR__."/ReplaceTest/Templates/test_layout3.txt",
            ),
            array('%placeholder1%' => 'value1', '%placeholder2%' => 'value2')
        );

        $this->assertStringEqualsFile(
            __DIR__."/ReplaceTest/ExpectedResult/layoutsRenderingCustomPlaceholder.txt",
            $result
        );

        // placeholder not exists in layouts
        $view->setLayoutContentPlaceholder('%nonexistent_placeholder%');
        $result = $view->render(
            array(
                __DIR__."/ReplaceTest/Templates/test_template.txt",
                __DIR__."/ReplaceTest/Templates/test_layout3.txt",
            ),
            array('%placeholder1%' => 'value1', '%placeholder2%' => 'value2')
        );

        $this->assertStringEqualsFile(
            __DIR__."/ReplaceTest/ExpectedResult/layoutsRenderingCustomPlaceholderNotReplaced.txt",
            $result
        );
    }
}