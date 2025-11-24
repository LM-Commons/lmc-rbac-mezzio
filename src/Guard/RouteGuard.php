<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ServerRequestInterface;

use function array_keys;
use function fnmatch;
use function in_array;
use function is_int;

use const FNM_CASEFOLD;

class RouteGuard extends AbstractGuard
{
    use ProtectionPolicyTrait;

    protected array $rules = [];

    public function __construct(
        private readonly RoleServiceInterface $roleService,
        array $rules = [],
        string $protectionPolicy = GuardInterface::POLICY_ALLOW
    ) {
        $this->setRules($rules);
        $this->setProtectionPolicy($protectionPolicy);
    }

    public function isGranted(ServerRequestInterface $request): bool
    {
        /** @var RouteResult $routeResult */
        $routeResult      = $request->getAttribute(RouteResult::class);
        $matchedRouteName = $routeResult->getMatchedRouteName();
        /** @var null|array $allowedRoles */
        $allowedRoles     = null;

        foreach (array_keys($this->rules) as $routeRule) {
            if (fnmatch($routeRule, $matchedRouteName, FNM_CASEFOLD)) {
                $allowedRoles = $this->rules[$routeRule];
                break;
            }
        }

        if (null === $allowedRoles) {
            return $this->protectionPolicy === GuardInterface::POLICY_ALLOW;
        }

        if (in_array('*', $allowedRoles)) {
            return true;
        }

        $identity = $request->getAttribute(UserInterface::class);
        return $this->roleService->matchIdentityRoles($identity, $allowedRoles);
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
