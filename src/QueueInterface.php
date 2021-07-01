<?php

declare(strict_types=1);

namespace Pollen\Asset;

interface QueueInterface
{
    /**
     * Get after render HTML content.
     *
     * @return string
     */
    public function after(): string;

    /**
     * Get before render HTML content.
     *
     * @return string
     */
    public function before(): string;

    /**
     * Get render HTML content.
     *
     * @return string
     */
    public function content(): string;

    /**
     * Get priority order.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Check if asset is displayed in HTML footer.
     *
     * @return bool
     */
    public function inFooter(): bool;

    /**
     * Asset render in HTML page.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Set after render HTML content.
     *
     * @param string $after
     *
     * @return static
     */
    public function setAfter(string $after): QueueInterface;

    /**
     * Set before render HTML content.
     *
     * @param string $before
     *
     * @return static
     */
    public function setBefore(string $before): QueueInterface;

    /**
     * Set HTML render attributes.
     *
     * @param string|array $key
     * @param mixed|null $value
     *
     * @return static
     */
    public function setHtmlAttrs($key, $value = null): QueueInterface;


    /**
     * Set asset displayed in HTML footer.
     *
     * @param bool $inFooter
     *
     * @return static
     */
    public function setInFooter(bool $inFooter = true): QueueInterface;

    /**
     * Set queue priority order.
     *
     * @param int $priority
     *
     * @return static
     */
    public function setPriority(int $priority): QueueInterface;

    /**
     * Serialize to an array.
     *
     * @return array
     */
    public function toArray(): array;
}