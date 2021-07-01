<?php

declare(strict_types=1);

namespace Pollen\Asset;

use Pollen\Support\Proxy\AssetProxy;
use Pollen\Support\Html;

abstract class Queue implements QueueInterface
{
    use AssetProxy;

    /**
     * High priority.
     *
     * @const int
     */
    public const HIGH = 100;

    /**
     * Normal priority.
     *
     * @const int
     */
    public const NORMAL = 0;

    /**
     * Low priority.
     *
     * @const int
     */
    public const LOW = -100;

    /**
     * Priority order.
     * @var int
     */
    protected int $priority = 0;

    /**
     * Before render tag HTML.
     * @var string
     */
    protected string $before = '';

    /**
     * After render tag HTML.
     * @var string
     */
    protected string $after = '';

    /**
     * Render tag HTML attributes.
     * @var array
     */
    protected array $htmlAttrs = [];

    /**
     * In HTML footer displayed indicator.
     * @var bool
     */
    protected bool $inFooter = false;

    /**
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(array $htmlAttrs = [], int $priority = self::NORMAL)
    {
        $this->htmlAttrs = $htmlAttrs;
        $this->priority = $priority;
    }

    /**
     * Instance resolved as string, return queue HTML render.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function after(): string
    {
        return $this->after;
    }

    /**
     * @inheritDoc
     */
    public function before(): string
    {
        return $this->before;
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function inFooter(): bool
    {
        return $this->inFooter;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('%s %s %s', $this->before(), $this->content(), $this->after());
    }

    /**
     * @inheritDoc
     */
    public function setAfter(string $after): QueueInterface
    {
        $this->after = $after;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBefore(string $before): QueueInterface
    {
        $this->before = $before;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtmlAttrs($key, $value = null): QueueInterface
    {
        if (!is_array($key)) {
            $attrs = ($value !== null) ? [$key => $value] : [$key];
        } else {
            $attrs = $key;
        }

        $newAttrs = (new Html())->tagAttributes($attrs, false);

        $this->htmlAttrs = array_merge($this->htmlAttrs, $newAttrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setInFooter(bool $inFooter = true): QueueInterface
    {
        $this->inFooter = $inFooter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): QueueInterface
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'priority'  => $this->priority,
            'in_footer' => $this->inFooter(),
            'render'    => $this->render(),
        ];
    }
}