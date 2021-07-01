<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface AssetQueueInterface extends QueueInterface
{
    /**
     * Add asset dependency.
     *
     * @param string $name
     *
     * @return AssetQueueInterface
     */
    public function addDependency(string $name): AssetQueueInterface;

    /**
     * Check if minification is enabled.
     *
     * @return bool
     */
    public function isMinifyEnabled(): bool;

    /**
     * Enable|Disable minification.
     *
     * @param bool $enabled
     *
     * @return static
     */
    public function setMinify(bool $enabled = true): AssetQueueInterface;
}