<?php
namespace NEOSidekick\HtmlContent\Tests\Unit\Eel;

/*
 * This file is part of the NEOSidekick.HtmlContent package.
 */

use NEOSidekick\HtmlContent\Eel\Helper;
use Neos\Flow\Tests\UnitTestCase;

class HelperTest extends UnitTestCase
{
    /**
     * @test
     */
    public function postProcessHtmlHandlesValidHtmlWithScript()
    {
        $helper = new Helper();
        $html = '<script></script><h1>Valid H1</h1>';
        $result = $helper->postProcessHtml($html);

        $this->assertEquals('<script></script><h1>Valid H1</h1>', $result->getHtml());
        $this->assertFalse($result->hasErrors());
        $this->assertFalse($result->hasAutofixedClosingTags());
    }

    /**
     * @test
     */
    public function postProcessHtmlPreservesScriptTagsAtBeginning()
    {
        $helper = new Helper();
        $html = '<script></script><h1>';
        $result = $helper->postProcessHtml($html);

        // Expectation: Script tag is preserved, H1 is closed automatically
        $this->assertEquals('<script></script><h1></h1>', $result->getHtml());
        $this->assertFalse($result->hasErrors());
        $this->assertTrue($result->hasAutofixedClosingTags());
    }
}
