<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface InlineAssetInterface
{
    /**
     * Get contents.
     *
     * @return string
     */
    public function getContents(): string;
}