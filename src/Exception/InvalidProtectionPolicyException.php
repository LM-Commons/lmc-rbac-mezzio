<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Exception;

use RuntimeException;

class InvalidProtectionPolicyException extends RuntimeException implements ExceptionInterface
{
}
