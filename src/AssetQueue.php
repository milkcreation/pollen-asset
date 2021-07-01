<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Support\Proxy\AssetProxy;

abstract class AssetQueue extends Queue implements AssetQueueInterface
{
    use AssetProxy;

    /**
     * Related assets.
     * @var AssetInterface
     */
    protected AssetInterface $asset;

    /**
     * Assets names dependencies.
     * @var string[]
     */
    protected array $dependencies = [];

    /**
     * Minification indicator.
     * @var bool
     */
    protected ?bool $minify = null;

    /**
     * @param AssetInterface $asset
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(AssetInterface $asset, array $htmlAttrs = [], int $priority = self::NORMAL)
    {
        $this->asset = $asset;

        parent::__construct($htmlAttrs, $priority);
    }

    /**
     * @inheritDoc
     */
    public function addDependency(string $name): AssetQueueInterface
    {
        $this->dependencies[] = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isMinifyEnabled(): bool
    {
        return $this->minify ?: false;
    }

    /**
     * @inheritDoc
     */
    public function setMinify(bool $enabled = true): AssetQueueInterface
    {
        $this->minify = $enabled;

        return $this;
    }
}