<?php

declare(strict_types=1);

namespace Pollen\Asset;

Interface AssetProxyInterface
{
    /**
     * Instance du gestionnaire des assets.
     *
     * @return AssetManagerInterface
     */
    public function asset(): AssetManagerInterface;

    /**
     * Définition du gestionnaire des assets.
     *
     * @param AssetManagerInterface $assetManager
     *
     * @return AssetProxy|AssetProxyInterface|static
     */
    public function setAssetManager(AssetManagerInterface $assetManager): self;
}