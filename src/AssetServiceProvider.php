<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Container\BootableServiceProvider;

class AssetServiceProvider extends BootableServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        AssetManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(AssetManagerInterface::class, function () {
            return new AssetManager([], $this->getContainer());
        });
    }
}