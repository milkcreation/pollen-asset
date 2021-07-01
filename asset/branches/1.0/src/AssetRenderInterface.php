<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface AssetRenderInterface
{
    /**
     * Linearized HTML tag attributes.
     *
     * @return string
     */
    public function htmlAttrs(): string;

    /**
     * Asset HTML render.
     *
     * @return string
     */
    public function render(): string;
}
