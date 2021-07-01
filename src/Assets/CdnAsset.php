<?php

declare(strict_types=1);

namespace Pollen\Asset\Assets;

use Pollen\Asset\Asset;
use Pollen\Asset\AssetInterface;
use Pollen\Asset\UrlAssetInterface;
use Pollen\Validation\Validator as v;

class CdnAsset extends Asset implements UrlAssetInterface
{
    /**
     * Asset base url.
     * @var string|null
     */
    protected ?string $baseUrl = null;

    /**
     * Asset file path.
     * @var string
     */
    protected string $path;

    /**
     * Asset file url.
     * @var string|null
     */
    protected ?string $url = null;

    /**
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path)
    {
        $this->path = $path;

        parent::__construct($name);
    }

    /**
     * Get asset url from instance call as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUrl();
    }

    /**
     * Get base url.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        if ($this->baseUrl === null) {
            if ($package = $this->getPackage()) {
                $this->baseUrl = $package->getBaseUrl();
            } else {
                $this->baseUrl = null;
            }
        }
        return $this->baseUrl ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        if ($this->url === null) {
            if (v::url()->validate($this->path)) {
                $this->url = $this->path;
            } else {
                $this->url = $this->normalizePath($this->getBaseUrl() . '/' . $this->path);
            }
        }

        return $this->url;
    }

    /**
     * Set asset file base url.
     *
     * @param string $baseUrl
     *
     * @return static
     */
    public function setBaseUrl(string $baseUrl): AssetInterface
    {
        $this->baseUrl = $this->normalizePath($baseUrl);

        return $this;
    }
}