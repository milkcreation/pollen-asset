<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Queue;
use Pollen\Asset\Renders\TitleTagRender;

class TitleTagQueue extends Queue
{
    /**
     * Normal priority.
     *
     * @const int
     */
    public const NORMAL = 80;

    /**
     * Meta tag title contents.
     * @var string
     */
    protected string $title;

    /**
     * @param string $title
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(string $title, array $htmlAttrs = [], int $priority = self::NORMAL)
    {
        $this->title = $title;

        parent::__construct($htmlAttrs, $priority);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return (new TitleTagRender($this->title, $this->htmlAttrs))->render();
    }
}