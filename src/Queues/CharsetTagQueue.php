<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Queue;
use Pollen\Asset\Renders\MetaTagRender;

class CharsetTagQueue extends Queue
{
    /**
     * Normal priority.
     *
     * @const int
     */
    public const NORMAL = 90;

    /**
     * Charset encoding.
     * @var string
     */
    protected string $charset;

    /**
     * @param string $charset
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(string $charset = 'UTF-8', array $htmlAttrs = [], int $priority = self::NORMAL)
    {
        $this->charset = $charset;

        parent::__construct($htmlAttrs, $priority);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        $this->htmlAttrs['charset'] = $this->charset;

        return (new MetaTagRender($this->htmlAttrs))->render();
    }
}