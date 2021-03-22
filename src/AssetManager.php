<?php

declare(strict_types=1);

namespace Pollen\Asset;

use InvalidArgumentException;
use MatthiasMullie\Minify\CSS as MinifyCss;
use MatthiasMullie\Minify\JS as MinifyJs;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;
use RuntimeException;
use Throwable;

class AssetManager implements AssetManagerInterface
{
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * @var array
     */
    private $headerJsVarsKeys = [];

    /**
     * @var array
     */
    protected $assetsBag = [];

    /**
     * @var string[]
     */
    protected $inlineCss = [];

    /**
     * @var string[]
     */
    protected $headerInlineJs = [];

    /**
     * @var string[]
     */
    protected $footerInlineJs = [];

    /**
     * @var array
     */
    protected $footerGlobalJsVars = [];

    /**
     * @var array
     */
    protected $headerGlobalJsVars = [];

    /**
     * @var bool
     */
    protected $minifyCss = false;

    /**
     * @var bool
     */
    protected $minifyJs = false;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): AssetManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function addGlobalJsVar(string $key, $value, bool $inFooter = false, ?string $namespace = 'app'): AssetManagerInterface
    {
        $argKey = $inFooter ? 'footerGlobalJsVars' : 'headerGlobalJsVars';

        if ($namespace === null) {
            $this->{$argKey}[$key] = $value;
        } else {
            $this->{$argKey}[$namespace] = $this->{$argKey}[$namespace] ?? [];
            $this->{$argKey}[$namespace][$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addInlineCss(string $css): AssetManagerInterface
    {
        $this->inlineCss[] = $css;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addInlineJs(string $js, bool $inFooter = false): AssetManagerInterface
    {
        if ($inFooter) {
            $this->footerInlineJs[] = $js;
        } else {
            $this->headerInlineJs[] = $js;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->assetsBag;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool
    {
        return !empty($this->assetsBag);
    }

    /**
     * @inheritDoc
     */
    public function footerScripts(): string
    {
        $concatJs = '';
        foreach ($this->footerGlobalJsVars as $key => $vars) {
            if (is_array($vars) && in_array($key, $this->headerJsVarsKeys, true)) {
                foreach ($vars as $k => $v) {
                    $concatJs .= "{$key}['$k']=" . $this->normalizeVars($v) . ";";
                }
            } else {
                $concatJs .= "let {$key}=" . $this->normalizeVars($vars) . ";";
            }
        }

        foreach ($this->footerInlineJs as $inlineJs) {
            $concatJs .= $this->normalizeStr($inlineJs) . ";";
        }

        if ($concatJs && $this->minifyJs) {
            $concatJs = (new MinifyJs($concatJs))->minify();
        }

        return $concatJs ? "<script type=\"text/javascript\">/* <![CDATA[ */{$concatJs}/* ]]> */</script>": '';
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, string $default = ''): string
    {
        return $this->assetsBag[$key] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function headerStyles(): string
    {
        $concatCss = '';
        foreach ($this->inlineCss as $inlineCss) {
            $concatCss .= $this->normalizeStr($inlineCss);
        }

        if (empty($concatCss)) {
            return '';
        }

        if ($concatCss && $this->minifyCss) {
            $concatCss = (new MinifyCss($concatCss))->minify();
        }

        return $concatCss ? "<style type=\"text/css\">{$concatCss}</style>": '';
    }

    /**
     * @inheritDoc
     */
    public function headerScripts(): string
    {
        $concatJs = '';
        foreach ($this->headerGlobalJsVars as $key => $vars) {
            $this->headerJsVarsKeys[] = $key;
            $concatJs .= "let {$key}=" . $this->normalizeVars($vars) . ";";
        }

        foreach ($this->headerInlineJs as $inlineJs) {
            $concatJs .= $this->normalizeStr($inlineJs) . ";";
        }

        if ($concatJs && $this->minifyJs) {
            $concatJs = (new MinifyJs($concatJs))->minify();
        }

        return $concatJs ? "<script type=\"text/javascript\">/* <![CDATA[ */{$concatJs}/* ]]> */</script>" : '';
    }

    /**
     * @inheritDoc
     */
    public function set($key, ?string $value = ''): AssetManagerInterface
    {
        if (is_string($key)) {
            $key = [$key => $value];
        }

        if (is_array($key)) {
            foreach($key as $k => $v) {
                if (is_string($v)) {
                    $this->assetsBag[$k] = $v;
                }
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setManifestJson(string $manifestJson, callable $fallback = null): AssetManagerInterface
    {
        if (!file_exists($manifestJson)) {
            throw new RuntimeException('Assets Manifest Json file unavailable');
        }

        try {
            $content = file_get_contents($manifestJson);
            $datas = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new RuntimeException('Assets Manifest Json file invalid');
        }

        if ($fallback !== null) {
            array_walk($datas, $fallback);
        }

       $this->set($datas);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMinifyCss(bool $minify = true): AssetManagerInterface
    {
        $this->minifyCss = $minify;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMinifyJs(bool $minify = true): AssetManagerInterface
    {
        $this->minifyJs = $minify;

        return $this;
    }

    /**
     * Normalisation d'un chaîne de caractères.
     *
     * @param string $str
     *
     * @return string
     */
    protected function normalizeStr(string $str): string
    {
        return html_entity_decode(rtrim(trim($str), ';'), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Normalisation de variables.
     *
     * @param array|string|int|bool $vars
     *
     * @return string
     */
    protected function normalizeVars($vars): string
    {
        if (is_array($vars)) {
            foreach ($vars as $k => &$v) {
                if (is_scalar($v)) {
                    $v = (is_bool($v) || is_int($v)) ? $v : $this->normalizeStr((string)$v);
                }
            }
            unset($v);

            try {
                $vars = json_encode($vars, JSON_THROW_ON_ERROR);
            } catch (Throwable $e) {
                $vars = '';
            }
        } elseif (is_scalar($vars)) {
            $vars = (is_bool($vars) || is_int($vars)) ? $vars : "'". $this->normalizeStr((string)$vars) . "'";
        } else {
            throw new InvalidArgumentException(
                'Type of Asset vars are invalid. Only scalar or array of scalar allowed.'
            );
        }

        return (string)$vars;
    }
}
