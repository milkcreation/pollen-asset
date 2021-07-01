<?php

declare(strict_types=1);

namespace Pollen\Asset\Renders;

use Pollen\Asset\AssetRender;

class TitleTagRender extends AssetRender
{
    /**
     * Title tag contents.
     * @var string
     */
    protected string $title;

    /**
     * @param string $title
     * @param array $htmlAttrs
     */
    public function __construct(string $title, array $htmlAttrs = [])
    {
        $this->title = $title;

        parent::__construct($htmlAttrs);
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return "<title {$this->htmlAttrs()}>$this->title</title>";
    }
}