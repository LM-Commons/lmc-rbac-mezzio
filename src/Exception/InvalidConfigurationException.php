<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Exception;

use RuntimeException;

final class InvalidConfigurationException extends RuntimeException implements ExceptionInterface
{
}
