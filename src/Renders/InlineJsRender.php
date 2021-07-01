<?php

declare(strict_types=1);

namespace Pollen\Asset\Renders;

use Pollen\Asset\AssetRender;

class InlineJsRender extends AssetRender
{
    /**
     * Inline Js tag contents.
     * @var string
     */
    protected string $inlineContents;

    /**
     * @param string $inlineContents
     * @param array $htmlAttrs
     */
    public function __construct(string $inlineContents, array $htmlAttrs = [])
    {
        $this->inlineContents = $inlineContents;

        parent::__construct($htmlAttrs);
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $this->htmlAttrs['type'] = 'text/javascript';

        return "<script {$this->htmlAttrs()}>/* <![CDATA[ */" . $this->inlineContents . "/* ]]> */</script>";
    }
}