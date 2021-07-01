<?php

declare(strict_types=1);

namespace Pollen\Asset\Renders;

use Pollen\Asset\AssetRender;

class JsTagRender extends AssetRender
{
    /**
     * Js script tag url.
     * @var string
     */
    protected string $url;

    /**
     * @param string $url
     * @param array $htmlAttrs
     */
    public function __construct(string $url, array $htmlAttrs = [])
    {
        $this->url = $url;

        parent::__construct($htmlAttrs);
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $this->htmlAttrs['type'] = 'text/javascript';
        $this->htmlAttrs['src'] = $this->url;

        return "<script {$this->htmlAttrs()}></script>";
    }
}
