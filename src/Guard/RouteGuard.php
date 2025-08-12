<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Service\RoleServiceInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

class RouteGuard extends AbstractGuard
{
    use ProtectionPolicyTrait;

    protected array $rules = [];

    public function __construct(
        private readonly RoleServiceInterface $roleService,
        array $rules = []
    ) {
        $this->setRules($rules);
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        $matchedRouteName = $request->getAttribute(RouteResult::class);
    }

    public function setRules(array $rules): void
    {
        $this->rules = [];

        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                $routeRegex = $value;
                $roles      = [];
            } else {
                $routeRegex = $key;
                $roles      = (array) $value;
            }

            $this->rules[$routeRegex] = $roles;
        }
    }
}
