<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Queue;

class HtmlQueue extends Queue
{
    /**
     * Html render contents.
     * @var string
     */
    protected string $html;

    /**
     * @param string $html
     * @param bool $inFooter
     * @param int $priority
     */
    public function __construct(string $html, bool $inFooter = false, int $priority = self::NORMAL)
    {
        $this->html = $html;
        $this->inFooter = $inFooter;

        parent::__construct([], $priority);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return $this->html;
    }
}