<?php

declare(strict_types=1);

namespace Pollen\Asset\Queues;

use Pollen\Asset\Queue;
use Pollen\Asset\Renders\LinkTagRender;

class LinkTagQueue extends Queue
{
    /**
     * Normal priority.
     * @const int
     */
    public const NORMAL = 65;

    /**
     * Link tag href attribute.
     * @var string
     */
    protected string $href;

    /**
     * Link tag rel attribute.
     * @var string|null
     */
    protected ?string $rel = null;

    /**
     * @param string $href
     * @param string|null $rel
     * @param array $htmlAttrs
     * @param int $priority
     */
    public function __construct(string $href, ?string $rel = null, array $htmlAttrs = [], int $priority = self::NORMAL)
    {
        $this->href = $href;

        if ($rel !== null) {
            $this->rel = $rel;
        }

        parent::__construct($htmlAttrs, $priority);
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        if($this->rel !== null) {
            $this->htmlAttrs['rel'] = $this->rel;
        }
        $this->htmlAttrs['href'] = $this->href;

        return (new LinkTagRender($this->htmlAttrs))->render();
    }
}