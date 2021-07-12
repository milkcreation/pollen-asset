<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\AssetQueue;
use Pollen\Asset\InlineAssetInterface;
use Pollen\Asset\Renders\CssTagRender;
use Pollen\Asset\Renders\InlineCssRender;
use Pollen\Asset\UrlAssetInterface;

class CssAssetQueue extends AssetQueue
{
    /**
     * Normal priority.
     * @const int
     */
    public const NORMAL = 25;

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        if ($this->asset instanceof UrlAssetInterface) {
            return (new CssTagRender($this->asset->getUrl(), $this->htmlAttrs))->render();
        }

        if ($this->asset instanceof InlineAssetInterface) {
            return (new InlineCssRender($this->asset->getContents(), $this->htmlAttrs))->render();
        }

        return '';
    }
}