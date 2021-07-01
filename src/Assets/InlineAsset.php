<?php

declare(strict_types=1);

namespace Pollen\Asset\Assets;

use Pollen\Asset\Asset;
use Pollen\Asset\InlineAssetInterface;

class InlineAsset extends Asset implements InlineAssetInterface
{
    /**
     * Asset contents.
     * @var string
     */
    protected string $contents;

    /**
     * @param string $name
     * @param string $contents
     */
    public function __construct(string $name, string $contents)
    {
        $this->contents = $contents;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        return $this->contents;
    }
}