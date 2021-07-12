<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Support\Proxy\AssetProxy;

abstract class Asset implements AssetInterface
{
    use AssetProxy;

    /**
     * Asset name.
     * @var string
     */
    protected string $name;

    /**
     * Asset related package.
     * @var AssetPackageInterface|null
     */
    protected ?AssetPackageInterface $package = null;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPackage(): ?AssetPackageInterface
    {
        return $this->package;
    }

    /**
     * @inheritDoc
     */
    public function setPackage(AssetPackageInterface $package): AssetInterface
    {
        $this->package = $package;

        return $this;
    }

    /**
     * Path normalization.
     *
     * @param string $path
     * @param string $sep
     *
     * @return string
     */
    protected function normalizePath(string $path, string $sep = '/'): string
    {
        $chunks = [];
        foreach (explode($sep, $path) as $chunk) {
            if (!empty($chunk)) {
                $chunks[] = $chunk;
            }
        }

        return ltrim(rtrim(implode($sep, $chunks), $sep), $sep);
    }
}