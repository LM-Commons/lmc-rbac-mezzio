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
     */
    protected string $template = 'error::403';

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
