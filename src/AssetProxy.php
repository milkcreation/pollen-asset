<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Psr\Container\ContainerInterface as Container;
use RuntimeException;

/**
 * @see \Pollen\Asset\AssetProxyInterface
 */
trait AssetProxy
{
    /**
     * Instance du gestionnaire de politique de confidentialité.
     * @var AssetManagerInterface|null
     */
    private $assetManager;

    /**
     * Instance du gestionnaire de politique de confidentialité.
     *
     * @return AssetManagerInterface
     */
    public function asset(): AssetManagerInterface
    {
        if ($this->assetManager === null) {
            $container = method_exists($this, 'getContainer') ? $this->getContainer() : null;

            if ($container instanceof Container && $container->has(AssetManagerInterface::class)) {
                $this->assetManager = $container->get(AssetManagerInterface::class);
            } else {
                try {
                    $this->assetManager = AssetManager::getInstance();
                } catch(RuntimeException $e) {
                    $this->assetManager = new AssetManager();
                }
            }
        }

        return $this->assetManager;
    }

    /**
     * Définition du gestionnaire de politique de confidentialité.
     *
     * @param AssetManagerInterface $assetManager
     *
     * @return AssetProxy|AssetProxyInterface|static
     */
    public function setGdpr(AssetManagerInterface $assetManager): self
    {
        $this->assetManager = $assetManager;

        return $this;
    }
}