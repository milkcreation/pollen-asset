<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface AssetManagerInterface extends ContainerProxyInterface
{
    /**
     * Ajout d'une variable JS.
     *
     * @param string $key Clé d'indice.
     * @param mixed $value Valeur.
     * @param boolean $inFooter.
     * @param string|null $namespace
     *
     * @return static
     */
    public function addGlobalJsVar(string $key, $value, bool $inFooter = true, ?string $namespace = 'app'): AssetManagerInterface;

    /**
     * Ajout de styles CSS en ligne.
     *
     * @param string $css
     *
     * @return static
     */
    public function addInlineCss(string $css): AssetManagerInterface;

    /**
     * Définition de styles JS.
     *
     * @param string $js
     * @param boolean $inFooter
     *
     * @return static
     */
    public function addInlineJs(string $js, bool $inFooter = false): AssetManagerInterface;

    /**
     * Récupération de la liste des assets déclarés.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Vérification d'existence des assets déclarés.
     *
     * @return bool
     */
    public function exists(): bool;

    /**
     * Récupération des scripts JS du pied de page du site.
     *
     * @return string
     */
    public function footerScripts(): string;

    /**
     * Récupération du chemin vers un asset.
     *
     * @param string $key
     * @param string $default
     *
     * @return string
     */
    public function get(string $key, string $default = ''): string;

    /**
     * Récupération des styles CSS de l'entête du site.
     *
     * @return string
     */
    public function headerStyles(): string;

    /**
     * Récupération des scripts JS de l'entête du site.
     *
     * @return string
     */
    public function headerScripts(): string;

    /**
     * Définition des assets.
     *
     * @param string|array $key
     * @param string|null $value
     *
     * @return static
     */
    public function set($key, string $value = ''): AssetManagerInterface;

    /**
     * Définition d'un fichier Manifest JSON de déclaration des assets.
     *
     * @param string $manifestJson
     * @param callable|null $fallback
     *
     * @return AssetManagerInterface
     */
    public function setManifestJson(string $manifestJson, callable $fallback = null): AssetManagerInterface;

    /**
     * Définition de la minification des styles CSS.
     *
     * @param bool $minify
     *
     * @return static
     */
    public function setMinifyCss(bool $minify = true): AssetManagerInterface;

    /**
     * Définition de la minification des scripts JS.
     *
     * @param bool $minify
     *
     * @return static
     */
    public function setMinifyJs(bool $minify = true): AssetManagerInterface;

}
