<?php

declare(strict_types=1);

namespace Pollen\Asset\Renders;

use Pollen\Asset\AssetRender;

class CssTagRender extends AssetRender
{
    /**
     * Css link tag url.
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
        $this->htmlAttrs['rel'] = 'stylesheet';
        $this->htmlAttrs['type'] = 'text/css';
        $this->htmlAttrs['href'] = $this->url;

        return "<link {$this->htmlAttrs()}>";
    }
}
