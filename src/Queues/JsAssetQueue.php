<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\AssetInterface;
use Pollen\Asset\AssetQueue;
use Pollen\Asset\InlineAssetInterface;
use Pollen\Asset\Renders\InlineJsRender;
use Pollen\Asset\Renders\JsTagRender;
use Pollen\Asset\UrlAssetInterface;

class JsAssetQueue extends AssetQueue
{
    /**
     * @param AssetInterface $asset
     * @param bool $inFooter
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(
        AssetInterface $asset,
        bool $inFooter = false,
        array $htmlAttrs = [],
        int $priority = self::NORMAL
    ) {
        $this->inFooter = $inFooter;

        parent::__construct($asset, $htmlAttrs, $priority);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        if ($this->asset instanceof UrlAssetInterface) {
            return (new JsTagRender($this->asset->getUrl(), $this->htmlAttrs))->render();
        }

        if ($this->asset instanceof InlineAssetInterface) {
            return (new InlineJsRender($this->asset->getContents(), $this->htmlAttrs))->render();
        }

        return '';
    }
}