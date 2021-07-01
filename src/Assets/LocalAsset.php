<?php

declare(strict_types=1);

namespace Pollen\Asset\Assets;

use Pollen\Asset\Asset;
use Pollen\Asset\AssetInterface;
use Pollen\Asset\UrlAssetInterface;
use Pollen\Support\Filesystem as fs;
use SplFileInfo;

/**
 * @mixin SplFileInfo
 */
class LocalAsset extends Asset implements UrlAssetInterface
{
    /**
     * Asset file instance.
     * @var SplFileInfo
     */
    protected SplFileInfo $file;

    /**
     * Asset base path.
     * @var string|null
     */
    protected string $basePath;

    /**
     * Asset base url.
     * @var string|null
     */
    protected ?string $baseUrl = null;

    /**
     * Asset file relative path.
     * @var string|null
     */
    protected ?string $relPath = null;

    /**
     * Asset file url.
     * @var string|null
     */
    protected ?string $url = null;

    /**
     * @param string $name
     * @param SplFileInfo $file
     * @param string $basePath
     */
    public function __construct(string $name, SplFileInfo $file, string $basePath)
    {
        $this->file = $file;
        $this->basePath = fs::normalizePath($basePath);

        parent::__construct($name);
    }

    /**
     * Get asset file url from instance call as string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUrl();
    }

    /**
     * Delegate associated SplFileInfo instance method call.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->file->$method(...$arguments);
    }

    /**
     * Get base path.
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
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
     * Get file contents.
     *
     * @return string
     */
    public function getContents(): string
    {
        return file_exists($this->getRealPath()) ? file_get_contents($this->getRealPath()) : '';
    }

    /**
     * Get related file path.
     *
     * @return string
     */
    public function getRelPath(): string
    {
        if ($this->relPath === null) {
            if (preg_match('/^' . preg_quote($this->getBasePath(), fs::DS) . '/', $this->getRealPath())) {
                $this->relPath = preg_replace(
                    '/^' . preg_quote($this->getBasePath(), fs::DS) . '/', '', $this->getRealPath()
                );
            } else {
                $this->relPath = null;
            }
        }

        return $this->relPath ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        if ($this->url === null) {
            $this->url = $this->getBaseUrl() . $this->normalizePath($this->getRelPath());
        }
        return $this->url;
    }

    /**
     * Set file base path.
     *
     * @param string $basePath
     *
     * @return static
     */
    public function setBasePath(string $basePath): AssetInterface
    {
        $this->basePath = fs::normalizePath($basePath);

        return $this;
    }

    /**
     * Set file base url.
     *
     * @param string $baseUrl
     *
     * @return static
     */
    public function setBaseUrl(string $baseUrl): AssetInterface
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
}
