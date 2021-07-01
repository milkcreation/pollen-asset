<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Support\Html;

abstract class AssetRender implements AssetRenderInterface
{
    /**
     * Render tag HTML attributes.
     * @var array
     */
    protected array $htmlAttrs = [];

    /**
     * @param array $htmlAttrs
     */
    public function __construct(array $htmlAttrs = [])
    {
        $this->htmlAttrs = $htmlAttrs;
    }

    /**
     * Resolve instance as string and return HTML tag render.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function htmlAttrs(): string
    {
        return Html::attr($this->htmlAttrs);
    }
}
