<?php

declare(strict_types=1);

namespace Pollen\Asset;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface AssetPackageInterface extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Register an asset for the package.
     *
     * @param AssetInterface $asset
     *
     * @return static
     */
    public function addAsset(AssetInterface $asset): AssetPackageInterface;

    /**
     * Get package base url.
     *
     * @return string
     */
    public function getBaseUrl(): string;
}