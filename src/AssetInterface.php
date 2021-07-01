<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface AssetInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get related package.
     *
     * @return AssetPackageInterface|null
     */
    public function getPackage(): ?AssetPackageInterface;

    /**
     * Set related package.
     *
     * @param AssetPackageInterface $package
     *
     * @return static
     */
    public function setPackage(AssetPackageInterface $package): AssetInterface;
}