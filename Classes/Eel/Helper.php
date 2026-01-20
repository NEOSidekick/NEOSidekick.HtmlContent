<?php
declare(strict_types=1);

namespace NEOSidekick\HtmlContent\Eel;

use NEOSidekick\HtmlContent\Dto\HtmlParsingResultDto;
use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use DOMDocument;

/**
 * @Flow\Proxy(false)
 */
class Helper implements ProtectedContextAwareInterface
{
    public function isValidHtml($html = ''): bool
    {
        return !$this->postProcessHtml($html)->hasErrors();
    }

    public function postProcessHtml($html = ''): HtmlParsingResultDto
    {
        if (trim((string) $html) === '') {
            return new HtmlParsingResultDto((string)$html, '', []);
        }

        $internalErrorsInitialState = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new DOMDocument;
        // We wrap the HTML in a body tag to ensure that script tags are not moved to the head
        // We also add an XML encoding declaration to ensure that UTF-8 characters are parsed correctly
        $dom->loadHTML('<?xml encoding="utf-8" ?><body>' . $html . '</body>');
        $postProcessedBodyNode = $dom->getElementsByTagName('body')->item(0);
        $postProcessedHtml = '';
        if ($postProcessedBodyNode !== null) {
            $postProcessedHtml = str_replace(['<body>', '</body>'], '', $dom->saveHTML($postProcessedBodyNode));
        }
        // We filter some of the errors because they are false-positives
        // e.g. code 801 => wrong tag name
        // For more codes see: https://gnome.pages.gitlab.gnome.org/libxml2/devhelp/libxml2-xmlerror.html
        $result = [];
        foreach (libxml_get_errors() as $error) {
            // Skip unknown tags
            if ($error->code === 801) {
                continue;
            }
            // Only treat libxml errors as invalid (ignore warnings)
            if ((int)$error->level < 3) { // 3 = LIBXML_ERR_FATAL
                continue;
            }

            $result[] = $error;
        }
        libxml_clear_errors();

        libxml_use_internal_errors($internalErrorsInitialState);

        return new HtmlParsingResultDto($html, $postProcessedHtml, $result);
    }

    /**
     * All methods are considered safe
     */
    public function allowsCallOfMethod($methodName): bool
    {
        return true;
    }
}
