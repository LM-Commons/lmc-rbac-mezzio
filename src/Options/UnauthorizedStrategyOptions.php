<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Unauthorized strategy options
 *
 * @template TValue
 * @extends AbstractOptions<TValue>
 */
class UnauthorizedStrategyOptions extends AbstractOptions
{
    /**
     * Template to use
     *
     * @var non-empty-string
     */
    protected string $template = 'error::403';

    /**
     * @param non-empty-string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return non-empty-string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }
}
