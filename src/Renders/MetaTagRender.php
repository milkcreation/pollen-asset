<?php

declare(strict_types=1);

namespace Pollen\Asset\Renders;

use Pollen\Asset\AssetRender;

class MetaTagRender extends AssetRender
{
    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return "<meta {$this->htmlAttrs()}>";
    }
}