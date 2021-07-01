<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface UrlAssetInterface
{
    /**
     * Get file url.
     *
     * @return string
     */
    public function getUrl(): string;
}