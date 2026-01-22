<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Exception\InvalidConfigurationException;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Service\AuthorizationServiceInterface;
use Psr\Container\ContainerInterface;

use function assert;
use function count;
use function in_array;
use function is_array;
use function is_int;

final class RoutePermissionGuardFactory
{
    public function __invoke(ContainerInterface $container): RoutePermissionGuard
    {
        /** @var Options $moduleOptions */
        $moduleOptions = $container->get(Options::class);
        $rules         = $moduleOptions->getGuardOptions(RoutePermissionGuard::class);
        assert(is_array($rules));

        $rules = $this->marshallRules($rules);

        /** @psalm-suppress MixedArgument */
        return new RoutePermissionGuard(
            $container->get(AuthorizationServiceInterface::class),
            $rules,
            $moduleOptions->getProtectionPolicy(),
        );
    }

    private function marshallRules(array $rules): array
    {
        if (count($rules) === 0) {
            return $rules;
        }

        $marshalledRules = [];

        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeRegex  = $value;
                $permissions = [];
            } else {
                $routeRegex  = $key;
                $permissions = $this->marshallPermissions((array) $value);
            }

            $marshalledRules[$routeRegex] = $permissions;
        }
        return $marshalledRules;
    }

    private function marshallPermissions(array $permissions): array
    {
        if (count($permissions) === 0) {
            return [];
        }
        if (isset($permissions['permissions']) && ! is_array($permissions['permissions'])) {
            throw new InvalidConfigurationException('permissions should be an array');
        } elseif (
            isset($permissions['condition']) && ! in_array($permissions['condition'], [
                GuardInterface::CONDITION_AND,
                GuardInterface::CONDITION_OR,
            ])
        ) {
            throw new InvalidConfigurationException('condition must be either a GuardInterface::CONDITION_AND'
                . 'or GuardInterface::CONDITION_OR');
        }

        return $permissions;
    }
}
