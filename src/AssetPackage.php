<?php

declare(strict_types=1);

namespace Pollen\Asset;

use ArrayIterator;
use Pollen\Support\Proxy\AssetProxy;

class AssetPackage implements AssetPackageInterface
{
    use AssetProxy;

    /**
     * Package base url.
     * @var string
     */
    protected string $baseUrl;

    /**
     * Registered assets.
     * @var array<string, AssetInterface>
     */
    protected array $assets = [];

    /**
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function addAsset(AssetInterface $asset): AssetPackageInterface
    {
        $this->assets[$asset->getName()] = $asset;

        $this->asset()->addAsset($asset);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->assets);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->assets);
    }

    /**
     * @param mixed $offset
     *
     * @return AssetInterface|null
     */
    public function offsetGet($offset): ?AssetInterface
    {
        return $this->assets[$offset] ?? null;
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void {}

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
       return isset($this->assets[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void {}
}