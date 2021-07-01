<?php

declare(strict_types=1);

namespace Pollen\Asset\Packages;

use Pollen\Asset\AssetPackage;
use Pollen\Asset\Assets\CdnAsset;
use Pollen\Asset\Assets\LocalAsset;
use Pollen\Asset\Exception\InvalidJsonManifestException;
use Pollen\Asset\Exception\UnavailableJsonManifestException;
use Pollen\Support\Filesystem as fs;
use SplFileInfo;
use Throwable;

class JsonManifestPackage extends AssetPackage
{
    /**
     * Assets base path.
     * @var string|null
     */
    protected ?string $basePath = null;

    /**
     * @param string $jsonManifestFilename
     * @param string $baseUrl
     * @param string|null $basePath
     */
    public function __construct(string $jsonManifestFilename, string $baseUrl, ?string $basePath = null)
    {
        if (!file_exists($jsonManifestFilename)) {
            throw new UnavailableJsonManifestException(
                sprintf('Json manifest file [%s] is unavailable', $jsonManifestFilename)
            );
        }

        try {
            $assetDefs = json_decode(file_get_contents($jsonManifestFilename), true, 512, JSON_THROW_ON_ERROR);
        } catch(Throwable $e) {
            throw new InvalidJsonManifestException(
                sprintf('Json manifest file [%s] is invalid', $jsonManifestFilename)
            );
        }

        if ($basePath !== null) {
            $this->basePath = $basePath;
        }

        foreach ($assetDefs as $name => $path) {
           $asset = $this->basePath
                ? new LocalAsset(
                    $name,
                    new SplFileInfo(fs::normalizePath($this->basePath . fs::DS . $path)),
                    $this->basePath
                )
                : new CdnAsset($name, $path);
            $asset->setPackage($this);

            $this->addAsset($asset);
        }

        parent::__construct($baseUrl);
    }

    /**
     * Get package base path.
     *
     * @return string|null
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }
}