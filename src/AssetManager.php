<?php

declare(strict_types=1);

namespace Pollen\Asset;

use InvalidArgumentException;
use Illuminate\Support\Collection;
use Pollen\Asset\Assets\InlineAsset;
use Pollen\Asset\Queues\CharsetTagQueue;
use Pollen\Asset\Queues\CssAssetQueue;
use Pollen\Asset\Queues\HtmlQueue;
use Pollen\Asset\Queues\JsAssetQueue;
use Pollen\Asset\Queues\LinkTagQueue;
use Pollen\Asset\Queues\MetaTagQueue;
use Pollen\Asset\Queues\TitleTagQueue;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Filesystem as fs;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\EventProxy;
use Psr\Container\ContainerInterface as Container;
use Throwable;

class AssetManager implements AssetManagerInterface
{
    use ConfigBagAwareTrait;
    use ContainerProxy;
    use EventProxy;

    /**
     * Asset Manager main instance.
     * @var static|null
     */
    private static ?AssetManagerInterface $instance = null;

    /**
     * List of existing HTML head JS global variables names.
     * @var array
     */
    private array $headJsVarsNames = [];

    /**
     * List of registered assets instances.
     * @var AssetInterface[]
     */
    protected array $assets = [];

    /**
     * Assets Path.
     * @var string|null
     */
    protected ?string $basePath = null;

    /**
     * Assets Url.
     * @var string|null
     */
    protected ?string $baseUrl = null;

    /**
     * List of inline JS scripts to include in HTML footer.
     * @var string[]
     */
    protected array $footerInlineJs = [];

    /**
     * List of JS global variables to include in HTML footer.
     * @var array
     */
    protected array $footerGlobalJsVars = [];

    /**
     * List of inline JS scripts to include in HTML head.
     * @var string[]
     */
    protected array $headInlineJs = [];

    /**
     * List of JS global variables to include in HTML head.
     * @var array
     */
    protected array $headGlobalJsVars = [];

    /**
     * List of inline CSS styles to include in HTML head.
     * @var string[]
     */
    protected array $inlineCss = [];

    /**
     * List of queued assets.
     * @var array<string, QueueInterface>
     */
    protected array $queuedAssets = [];

    /**
     * List of queuing errors.
     * @var string[]
     */
    private array $queuingErrors = [];

    /**
     * List of asset render in HTML head queue.
     * @var array<string, string>|null
     */
    private ?array $headQueue = null;

    /**
     * List of asset render in HTML footer queue.
     * @var array<string, string>|null
     */
    private ?array $footerQueue = null;

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
     * Get Asset Manager main instance.
     *
     * @return static
     */
    public static function getInstance(): AssetManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function addGlobalJsVar(
        string $key,
        $value,
        bool $inFooter = false,
        ?string $namespace = 'app'
    ): AssetManagerInterface {
        $argKey = $inFooter ? 'footerGlobalJsVars' : 'headGlobalJsVars';

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
    public function addAsset(AssetInterface $asset): AssetManagerInterface
    {
        $this->assets[$asset->getName()] = $asset;

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
            $this->headInlineJs[] = $js;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->assets;
    }

    /**
     * @inheritDoc
     */
    public function dequeue(string $name): AssetManagerInterface
    {
        unset($this->queuedAssets[$name]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enqueue(QueueInterface $queue, ?string $name = null): AssetManagerInterface
    {
        if ($name !== null) {
            $this->queuedAssets[$name] = $queue;
        } else {
            $this->queuedAssets[] = $queue;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function enqueueCharset(string $charset = 'UTF-8',
        array $htmlAttrs = [],
        int $priority = TitleTagQueue::NORMAL,
        ?string $queueName = null
    ): QueueInterface {
        $this->enqueue(
            $queue = new CharsetTagQueue($charset, $htmlAttrs, $priority),
            $queueName ?? '_charset'
        );

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function enqueueCss(
        AssetInterface $asset,
        array $htmlAttrs = [],
        int $priority = CssAssetQueue::NORMAL,
        ?string $queueName = null
    ): AssetQueueInterface {
        if (!$this->get($asset->getName())) {
            $this->addAsset($asset);
        }

        $this->enqueue(
            $queue = new CssAssetQueue($asset, $htmlAttrs, $priority),
            $queueName ?? "css.{$asset->getName()}"
        );

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function enqueueHtml(
        string $html,
        bool $inFooter = false,
        int $priority = HtmlQueue::NORMAL,
        ?string $queueName = null
    ): QueueInterface {
        $this->enqueue($queue = new HtmlQueue($html, $inFooter, $priority), $queueName);

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function enqueueJs(
        AssetInterface $asset,
        bool $inFooter = false,
        array $htmlAttrs = [],
        int $priority = JsAssetQueue::NORMAL,
        ?string $queueName = null
    ): AssetQueueInterface {
        if (!$this->get($asset->getName())) {
            $this->addAsset($asset);
        }

        $this->enqueue(
            $queue = new JsAssetQueue($asset, $inFooter, $htmlAttrs, $priority),
            $queueName ?? "js.{$asset->getName()}"
        );

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function enqueueLink(
        string $rel,
        string $href,
        array $htmlAttrs = [],
        int $priority = LinkTagQueue::NORMAL,
        ?string $queueName = null
    ): QueueInterface {
        $this->enqueue(
            $queue = new LinkTagQueue($href, $rel, $htmlAttrs, $priority),
            $queueName ?? "_link.$rel"
        );

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function enqueueMeta(
        string $name,
        string $content,
        array $htmlAttrs = [],
        int $priority = MetaTagQueue::NORMAL,
        ?string $queueName = null
    ): QueueInterface {
        $this->enqueue(
            $queue = new MetaTagQueue($content, $name, $htmlAttrs, $priority),
            $queueName ?? "_meta.$name"
        );

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function enqueueTitle(
        string $title,
        array $htmlAttrs = [],
        int $priority = TitleTagQueue::NORMAL,
        ?string $queueName = null
    ): QueueInterface {
        $this->enqueue(
            $queue = new TitleTagQueue($title, $htmlAttrs, $priority),
            $queueName ?? '_title'
        );

        return $queue;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?AssetInterface
    {
        return $this->assets[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): ?string
    {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function getFooter(): string
    {
        $this->handleFooterQueue();

        return $this->footerQueue ? implode("\n", array_map([$this, 'mapRenderCallback'], $this->footerQueue)) : '';
    }
    /**
     * @inheritDoc
     */
    public function getHead(): string
    {
        $this->handleHeadQueue();

        return $this->headQueue ? implode("\n", array_map([$this, 'mapRenderCallback'], $this->headQueue)) : '';
    }

    /**
     * @inheritDoc
     */
    public function has(?string $name = null): bool
    {
        return $name !== null ? isset($this->assets[$name]) : !empty($this->assets);
    }

    /**
     * @inheritDoc
     */
    public function setBasePath(string $basePath): AssetManagerInterface
    {
        $this->basePath = fs::normalizePath($basePath);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBaseUrl(string $baseUrl): AssetManagerInterface
    {
        $this->baseUrl = fs::normalizePath($baseUrl);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(string $name): AssetManagerInterface
    {
        unset($this->assets[$name]);

        return $this;
    }

    /**
     * HTML head inline CSS concatenation.
     *
     * @return string
     */
    protected function concatHeadInlineCss(): string
    {
        $concatCss = '';
        foreach ($this->inlineCss as $inlineCss) {
            $concatCss .= $this->normalizeStr($inlineCss);
        }

        return $concatCss;
    }

    /**
     * HTML head global variables and inline JS concatenation.
     *
     * @return string
     */
    protected function concatHeadInlineJs(): string
    {
        $concatJs = '';
        foreach ($this->headGlobalJsVars as $key => $vars) {
            $this->headJsVarsNames[] = $key;
            $concatJs .= "let $key=" . $this->normalizeVars($vars) . ";";
        }

        foreach ($this->headInlineJs as $inlineJs) {
            $concatJs .= $this->normalizeStr($inlineJs) . ";";
        }

        return $concatJs;
    }

    /**
     * HTML footer global variables and inline JS concatenation.
     *
     * @return string
     */
    protected function concatFooterInlineJs(): string
    {
        $concatJs = '';
        foreach ($this->footerGlobalJsVars as $key => $vars) {
            if (is_array($vars) && in_array($key, $this->headJsVarsNames, true)) {
                foreach ($vars as $k => $v) {
                    $concatJs .= $key . "['$k']=" . $this->normalizeVars($v) . ";";
                }
            } else {
                $concatJs .= "let $key=" . $this->normalizeVars($vars) . ";";
            }
        }

        foreach ($this->footerInlineJs as $inlineJs) {
            $concatJs .= $this->normalizeStr($inlineJs) . ";";
        }

        return $concatJs;
    }

    /**
     * HTML footer queue handling.
     *
     * @return void
     */
    protected function handleFooterQueue(): void
    {
        if ($this->footerQueue === null) {
            $this->footerQueue = [];

            $this->event()->trigger('asset.handle-footer.before', [$this]);

            if ($jsContents = $this->concatFooterInlineJs()) {
                $this->enqueueJs(new InlineAsset('_footer-inline-js', $jsContents), true, [], JsAssetQueue::NORMAL + 1);
            }

            $this->event()->trigger('asset.handle-footer.collect', [$this]);

            $queueCollection = (new Collection($this->queuedAssets))
                ->filter(
                    function (QueueInterface $queue) {
                        return $queue->inFooter();
                    }
                )
                ->sortByDesc(
                    function (QueueInterface $queue) {
                        return $queue->getPriority();
                    }
                );

            /**
             * @var string $name
             * @var QueueInterface $queue
             */
            foreach ($queueCollection as $name => $queue) {
                $this->event()->trigger('asset.handle-footer.queue', [$name, $queue, $this]);
                $this->footerQueue[] = $queue->toArray();
            }

            $this->event()->trigger('asset.handle-footer.after', [$this]);
        }
    }

    /**
     * HTML head queue handling.
     *
     * @return void
     */
    protected function handleHeadQueue(): void
    {
        if ($this->headQueue === null) {
            $this->headQueue = [];

            $this->event()->trigger('asset.handle-head.before', [$this]);

            if ($cssContents = $this->concatHeadInlineCss()) {
                $this->enqueueCss(new InlineAsset('_head-inline-css', $cssContents), [], CssAssetQueue::NORMAL + 1);
            }

            if ($jsContents = $this->concatHeadInlineJs()) {
                $this->enqueueJs(new InlineAsset('_head-inline-js', $jsContents), false, [], JsAssetQueue::NORMAL + 1);
            }

            $this->event()->trigger('asset.handle-head.collect', [$this]);

            $queueCollection = (new Collection($this->queuedAssets))
                ->filter(
                    function (QueueInterface $queue) {
                        return !$queue->inFooter();
                    }
                )
                ->sortByDesc(
                    function (QueueInterface $queue) {
                        return $queue->getPriority();
                    }
                );

            /**
             * @var string $name
             * @var QueueInterface $queue
             */
            foreach ($queueCollection as $name => $queue) {
                $this->event()->trigger('asset.handle-head.queue', [$name, $queue, $this]);
                $this->headQueue[] = $queue->toArray();
            }

            $this->event()->trigger('asset.handle-head.after', [$this]);
        }
    }

    /**
     * String normalization.
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
     * Variables normalization.
     *
     * @param array|string|int|bool $vars
     *
     * @return string
     */
    protected function normalizeVars($vars): string
    {
        if (is_array($vars)) {
            foreach ($vars as &$v) {
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
            $vars = (is_bool($vars) || is_int($vars)) ? $vars : "'" . $this->normalizeStr((string)$vars) . "'";
        } else {
            throw new InvalidArgumentException(
                'Type of asset vars are invalid. Only scalar or array of scalar allowed.'
            );
        }

        return (string)$vars;
    }

    private function mapRenderCallback(array $queueArray): string
    {
        return $queueArray['render'] ?? '';
    }
}
