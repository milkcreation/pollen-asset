<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Support\Filesystem as fs;

class Asset implements AssetInterface
{
    /**
     * Gestionnaire des assets.
     * @var AssetManagerInterface
     */
    protected $assetManager;

    /**
     * Chemin de base vers le répertoire du fichier.
     * @var string|false
     */
    protected $baseDir;

    /**
     * Url de base vers le fichier.
     * @var string|false
     */
    protected $baseUrl;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name;

    /**
     * Chemin relatif vers le fichier.
     * @var string
     */
    protected $path;

    /**
     * Préfixe de l'url relative vers le fichier.
     * @var string|false
     */
    protected $relPrefix;

    /**
     * @param string $name
     * @param string $path
     * @param AssetManagerInterface $assetManager
     */
    public function __construct(string $name, string $path, AssetManagerInterface $assetManager)
    {
        $this->name = $name;
        $this->path = fs::normalizePath($path);
        $this->assetManager = $assetManager;
    }

    /**
     * Résolution de sortie de la classe sous la forme d'une chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getRelPrefix();
    }

    /**
     * @inheritDoc
     */
    public function contents(): string
    {
        return file_exists($this->filename()) ? file_get_contents($this->filename()) : '';
    }

    /**
     * @inheritDoc
     */
    public function disableBaseDir(): AssetInterface
    {
        $this->baseDir = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disableBaseUrl(): AssetInterface
    {
        $this->baseUrl = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disableRelPrefix(): AssetInterface
    {
        $this->relPrefix = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filename(): string
    {
        return $this->getBaseDir() . fs::DS . $this->getPath(true);
    }

    /**
     * @inheritDoc
     */
    public function getBaseDir(): string
    {
        if ($this->baseDir === null) {
            $this->baseDir = $this->assetManager->getBaseDir();
        }
        return $this->baseDir ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl(): string
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = $this->assetManager->getBaseUrl();
        }
        return $this->baseUrl ?: '';
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
    public function getPath(bool $normalized = false): string
    {
        return $normalized ? $this->normalizePath($this->path) : $this->path;
    }

    /**
     * @inheritDoc
     */
    public function getRelPrefix(): string
    {
        if ($this->relPrefix === null) {
            $this->relPrefix = $this->assetManager->getRelPrefix();
        }
        return $this->relPrefix ?: '';
    }

    /**
     * Normalisation d'un chemin.
     *
     * @param string $path
     * @param string $sep
     *
     * @return string
     */
    protected function normalizePath(string $path, string $sep = '/'): string
    {
        return ltrim(rtrim($path, $sep), $sep);
    }

    /**
     * @inheritDoc
     */
    public function rel(): string
    {
        return $this->getRelPrefix() . '/' . $this->getPath(true);
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return $this->getBaseUrl() . '/' . $this->getPath(true);
    }

    /**
     * @inheritDoc
     */
    public function setBaseDir(string $baseDir): AssetInterface
    {
        $this->baseDir = fs::normalizePath($baseDir);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl): AssetInterface
    {
        $this->baseUrl = fs::normalizePath($baseUrl);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRelPrefix(string $relPrefix): AssetInterface
    {
        $this->relPrefix = fs::normalizePath($relPrefix);

        return $this;
    }
}