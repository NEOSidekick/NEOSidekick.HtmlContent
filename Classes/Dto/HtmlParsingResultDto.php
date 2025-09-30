<?php

namespace NEOSidekick\HtmlContent\Dto;

use LibXMLError;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\ValueObject
 */
class HtmlParsingResultDto implements ProtectedContextAwareInterface
{
    /**
     * @var string
     */
    protected string $inputHtml;

    /**
     * @var string
     */
    protected string $html;

    /**
     * @var array<LibXMLError>
     */
    protected array $errors;

    /**
     * @var bool
     */
    protected bool $hasAutofixedClosingTags;

    public function __construct(string $inputHtml, string $postProcessedHtml, array $errors)
    {
        $this->inputHtml = $inputHtml;
        $this->html = $postProcessedHtml;
        $this->errors = $errors;
        // Determine if DOM added closing tags only when the input looked like actual HTML markup
        // Heuristic: consider as markup if it contains any angle brackets, otherwise treat as plain text
        $inputLooksLikeMarkup = (strpos($inputHtml, '<') !== false) || (strpos($inputHtml, '>') !== false);

        if (!$inputLooksLikeMarkup) {
            $this->hasAutofixedClosingTags = false;
        } else {
            // Ignore expansions of self-closing tags (<tag />) to <tag></tag>
            $inputSelfClosingCount = preg_match_all('/<\s*([a-zA-Z0-9:-]+)(\s+[^>]*)?\/>/m', $inputHtml, $m1) ?: 0;
            $postCloseCount = substr_count($postProcessedHtml, '</');
            $inputCloseCount = substr_count($inputHtml, '</');
            $closeDiff = $postCloseCount - $inputCloseCount;

            $this->hasAutofixedClosingTags = $closeDiff > max(0, $inputSelfClosingCount);
        }
    }

    public function getInputHtml(): string
    {
        return $this->inputHtml;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function hasAutofixedClosingTags(): bool
    {
        return $this->hasAutofixedClosingTags;
    }

    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
