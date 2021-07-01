<?php

declare(strict_types=1);

namespace Pollen\Asset\Renders;

use Pollen\Asset\AssetRender;

class LinkTagRender extends AssetRender
{
    /**
     * @inheritdoc
     */
    public function render(): string
    {
        return "<link {$this->htmlAttrs()}>";
    }
}