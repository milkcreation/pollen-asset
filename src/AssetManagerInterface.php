<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Asset\Queues\CssAssetQueue;
use Pollen\Asset\Queues\HtmlQueue;
use Pollen\Asset\Queues\JsAssetQueue;
use Pollen\Asset\Queues\LinkTagQueue;
use Pollen\Asset\Queues\MetaTagQueue;
use Pollen\Asset\Queues\TitleTagQueue;
use Pollen\Support\Proxy\ContainerProxyInterface;

interface AssetManagerInterface extends ContainerProxyInterface
{
    /**
     * Add a global JS variable.
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $inFooter
     * @param string|null $namespace
     *
     * @return static
     */
    public function addGlobalJsVar(
        string $key,
        $value,
        bool $inFooter = false,
        ?string $namespace = 'app'
    ): AssetManagerInterface;

    /**
     * Add new instance of an asset.
     *
     * @param AssetInterface $asset
     *
     * @return static
     */
    public function addAsset(AssetInterface $asset): AssetManagerInterface;

    /**
     * Add inline CSS styles.
     *
     * @param string $css
     *
     * @return static
     */
    public function addInlineCss(string $css): AssetManagerInterface;

    /**
     *  Add inline Js scripts.
     *
     * @param string $js
     * @param boolean $inFooter
     *
     * @return static
     */
    public function addInlineJs(string $js, bool $inFooter = false): AssetManagerInterface;

    /**
     * Get list of registered assets instances.
     *
     * @return AssetInterface[]|array
     */
    public function all(): array;

    /**
     * Remove an asset from queue.
     *
     * @return static
     */
    public function dequeue(string $name): AssetManagerInterface;

    /**
     * Add queue instance in queue.
     *
     * @param QueueInterface $queue
     * @param string|null $name
     *
     * @return static
     */
    public function enqueue(QueueInterface $queue, ?string $name = null): AssetManagerInterface;

    /**
     * Add a registered asset in CSS queue.
     *
     * @param AssetInterface $asset
     * @param array $htmlAttrs
     * @param int $priority
     *
     * @return AssetQueueInterface
     */
    public function enqueueCss(
        AssetInterface $asset,
        array $htmlAttrs = [],
        int $priority = CssAssetQueue::NORMAL
    ): AssetQueueInterface;

    /**
     * Add HTML contents in queue.
     *
     * @param string $html
     * @param bool $inFooter
     * @param int $priority
     *
     * @return QueueInterface
     */
    public function enqueueHtml(
        string $html,
        bool $inFooter = false,
        int $priority = HtmlQueue::NORMAL
    ): QueueInterface;

    /**
     * Add a registered asset in JS queue.
     *
     * @param AssetInterface $asset
     * @param bool $inFooter
     * @param array $htmlAttrs
     * @param int $priority
     *
     * @return AssetQueueInterface
     */
    public function enqueueJs(
        AssetInterface $asset,
        bool $inFooter = false,
        array $htmlAttrs = [],
        int $priority = JsAssetQueue::NORMAL
    ): AssetQueueInterface;

    /**
     * Add a link tag in queue.
     *
     * @param string $name
     * @param string $href
     * @param array $htmlAttrs
     * @param int $priority
     *
     * @return QueueInterface
     */
    public function enqueueLink(
        string $name,
        string $href,
        array $htmlAttrs = [],
        int $priority = LinkTagQueue::NORMAL
    ): QueueInterface;

    /**
     * Add a meta tag in queue.
     *
     * @param string $name
     * @param string $content
     * @param array $htmlAttrs
     * @param int $priority
     *
     * @return QueueInterface
     */
    public function enqueueMeta(
        string $name,
        string $content,
        array $htmlAttrs = [],
        int $priority = MetaTagQueue::NORMAL
    ): QueueInterface;

    /**
     * Add the meta tag title in queue.
     *
     * @param string $title
     * @param array $htmlAttrs
     * @param int $priority
     *
     * @return QueueInterface
     */
    public function enqueueTitle(
        string $title,
        array $htmlAttrs = [],
        int $priority = TitleTagQueue::NORMAL
    ): QueueInterface;

    /**
     * Get an registered asset instance.
     *
     * @param string $name
     *
     * @return AssetInterface|null
     */
    public function get(string $name): ?AssetInterface;

    /**
     * Get base path of assets.
     *
     * @return string|null
     */
    public function getBasePath(): ?string;

    /**
     * Get base url of assets.
     *
     * @return string|null
     */
    public function getBaseUrl(): ?string;

    /**
     * HTML head queue handling.
     *
     * @return string
     */
    public function handleHeadQueue(): string;

    /**
     * HTML footer queue handling.
     *
     * @return string
     */
    public function handleFooterQueue(): string;

    /**
     * Check if registered assets exists.
     *
     * @return bool
     */
    public function has(): bool;

    /**
     * Set base directory of assets.
     *
     * @param string $basePath
     *
     * @return static
     */
    public function setBasePath(string $basePath): AssetManagerInterface;

    /**
     * Set base url of assets.
     *
     * @param string $baseUrl
     *
     * @return static
     */
    public function setBaseUrl(string $baseUrl): AssetManagerInterface;
}
