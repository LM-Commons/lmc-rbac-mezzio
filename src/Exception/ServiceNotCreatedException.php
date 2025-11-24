<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Exception;

use RuntimeException;

final class ServiceNotCreatedException extends RuntimeException implements ExceptionInterface
{
}
