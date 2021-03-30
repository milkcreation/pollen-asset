<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface AssetInterface
{
    /**
     * Récupération du contenu du fichier.
     *
     * @return string
     */
    public function contents(): string;

    /**
     * Désactivation du chemin absolu vers le répertoire de dépôt des assets associé.
     *
     * @return static
     */
    public function disableBaseDir(): AssetInterface;

    /**
     * Désactivation de l'url absolue vers le répertoire de base de dépôt des assets associé.
     *
     * @return static
     */
    public function disableBaseUrl(): AssetInterface;

    /**
     * Désactivation du préfixe de l'url relative vers le répertoire de base de dépôt des assets associé.
     *
     * @return static
     */
    public function disableRelPrefix(): AssetInterface;

    /**
     * Récupération du chemin absolu vers le fichier.
     *
     * @return string
     */
    public function filename(): string;

    /**
     * Récupération du chemin absolu vers le répertoire du fichier.
     *
     * @return string
     */
    public function getBaseDir(): string;

    /**
     * Récupération de l'url absolue vers le répertoire du fichier.
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération du chemin relatif vers le fichier.
     *
     * @param bool $normalized
     *
     * @return string
     */
    public function getPath(bool $normalized = false): string;

    /**
     * Récupération du préfixe de l'url relative vers le répertoire du fichier.
     *
     * @return string
     */
    public function getRelPrefix(): string;

    /**
     * Récupération de l'url relative vers le fichier.
     *
     * @return string
     */
    public function rel(): string;

    /**
     * Récupération de l'url absolue vers le fichier.
     *
     * @return string
     */
    public function url(): string;

    /**
     * Définition du chemin de base vers le répertoire du fichier.
     *
     * @param string $baseDir
     *
     * @return static
     */
    public function setBaseDir(string $baseDir): AssetInterface;

    /**
     * Définition de l'url de base vers le répertoire du fichier.
     *
     * @param string $baseUrl
     *
     * @return static
     */
    public function setBaseUrl(string $baseUrl): AssetInterface;

    /**
     * Définition du préfixe de l'url relative vers le répertoire du fichier.
     *
     * @param string $relPrefix
     *
     * @return static
     */
    public function setRelPrefix(string $relPrefix): AssetInterface;
}