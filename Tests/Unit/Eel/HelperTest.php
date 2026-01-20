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
     * @dataProvider htmlDataProvider
     */
    public function postProcessHtmlTests($input, $expectedOutput, $expectErrors, $expectAutofix, $expectJavaScript = false)
    {
        $helper = new Helper();
        $result = $helper->postProcessHtml($input);

        $this->assertEquals($expectedOutput, $result->getHtml(), "HTML output mismatch for input: " . substr(print_r($input, true), 0, 50));

        if ($expectErrors) {
             $this->assertTrue($result->hasErrors(), "Expected errors but found none for input: " . substr(print_r($input, true), 0, 50));
        } else {
             $this->assertFalse($result->hasErrors(), "Expected no errors but found some for input: " . substr(print_r($input, true), 0, 50) . ". Errors: " . print_r($result->getErrors(), true));
        }

        if ($expectAutofix) {
            $this->assertTrue($result->hasAutofixedClosingTags(), "Expected autofix detection but found none for input: " . substr(print_r($input, true), 0, 50));
        } else {
            $this->assertFalse($result->hasAutofixedClosingTags(), "Expected no autofix detection but found some for input: " . substr(print_r($input, true), 0, 50));
        }

        if ($expectJavaScript) {
            $this->assertTrue($result->hasJavaScript(), "Expected JavaScript detection but found none for input: " . substr(print_r($input, true), 0, 50));
            $this->assertNotEquals($expectedOutput, $result->getHtmlWithoutJavaScript(), "Expected HTML without JavaScript to differ from original output");
        } else {
            $this->assertFalse($result->hasJavaScript(), "Expected no JavaScript detection but found some for input: " . substr(print_r($input, true), 0, 50));
             $this->assertEquals($expectedOutput, $result->getHtmlWithoutJavaScript(), "Expected HTML without JavaScript to be same as original output");
        }
    }

    public function htmlDataProvider()
    {
        // input, expectedOutput, expectErrors, expectAutofix, expectJavaScript
        return [
            // 1. Basic Parsing & Structure
            'basic div' => [
                '<div><p>Paragraph</p></div>',
                '<div><p>Paragraph</p></div>',
                false,
                false,
                false
            ],
            'mixed content' => [
                'Start <b>Bold</b> End',
                'Start <b>Bold</b> End',
                false,
                false,
                false
            ],
            'attributes' => [
                '<div class="my-class" data-test="value" id="main">Content</div>',
                '<div class="my-class" data-test="value" id="main">Content</div>',
                false,
                false,
                false
            ],

            // 2. Script & Style Tags (The Fix)
            'script at start' => [
                '<script>console.log("foo");</script><div>Content</div>',
                '<script>console.log("foo");</script><div>Content</div>',
                false,
                false,
                true
            ],
            'script with attributes' => [
                '<script src="app.js" type="module"></script>',
                '<script src="app.js" type="module"></script>',
                false,
                false,
                true
            ],
            'style tag' => [
                '<style>.red { color: red; }</style><h1>Title</h1>',
                '<style>.red { color: red; }</style><h1>Title</h1>',
                false,
                false,
                false
            ],

            // 3. UTF-8 & Encoding
            'umlauts' => [
                '<p>Hällo Wörld & € Symbol</p>',
                '<p>Hällo Wörld &amp; € Symbol</p>', // DOMDocument entity encodes special chars often
                false,
                false,
                false
            ],
            'emojis' => [
                '<p>🚀 🦄</p>',
                '<p>🚀 🦄</p>',
                false,
                false,
                false
            ],

            // 4. HTML5 & Unknown Tags
            'html5 tags' => [
                '<article><header><h1>Title</h1></header><section>Content</section></article>',
                '<article><header><h1>Title</h1></header><section>Content</section></article>',
                false,
                false,
                false
            ],
            'custom elements' => [
                '<my-custom-component>Content</my-custom-component>',
                '<my-custom-component>Content</my-custom-component>',
                false,
                false,
                false
            ],

            // 5. Auto-fix Detection
            'unclosed tag' => [
                '<div><p>Unclosed',
                '<div><p>Unclosed</p></div>',
                false,
                true,
                false
            ],
             'valid self closing' => [
                'Line break<br/>Image <img src="x" />',
                'Line break<br>Image <img src="x">', // DOMDocument normalizes <br/> to <br> in HTML mode
                false,
                false,
                false
            ],

            // 6. Edge Cases
            'empty string' => [
                '',
                '',
                false,
                false,
                false
            ],
            'null input' => [
                 null,
                 '',
                 false,
                 false,
                 false
            ],
            'plain text' => [
                'Just text',
                'Just text',
                false,
                false,
                false
            ],
             'comments' => [
                '<!-- This is a comment -->',
                '<!-- This is a comment -->',
                false,
                false,
                false
            ],
        ];
    }
}
