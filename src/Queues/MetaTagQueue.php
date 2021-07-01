<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Queue;
use Pollen\Asset\Renders\MetaTagRender;

class MetaTagQueue extends Queue
{
    /**
     * Normal priority.
     *
     * @const int
     */
    public const NORMAL = 75;

    /**
     * Meta tag content attribute.
     * @var string
     */
    protected string $content;

    /**
     * Meta tag name attribute.
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @param string $content
     * @param string|null $name
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(
        string $content,
        ?string $name = null,
        array $htmlAttrs = [],
        int $priority = self::NORMAL
    ) {
        $this->content = $content;

        if ($name !== null) {
            $this->name = $name;
        }

        parent::__construct($htmlAttrs, $priority);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        if ($this->name !== null) {
            $this->htmlAttrs['name'] = $this->name;
        }
        $this->htmlAttrs['content'] = $this->content;

        return (new MetaTagRender($this->htmlAttrs))->render();
    }
}