<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Override;
use Psr\Http\Message\ServerRequestInterface;

use function array_keys;
use function count;
use function fnmatch;
use function in_array;
use function is_int;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsString;

use const FNM_CASEFOLD;

/**
 * @final
 */
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

    #[Override]
    public function isGranted(ServerRequestInterface $request): bool
    {
        /** @var RouteResult $routeResult */
        $routeResult      = $request->getAttribute(RouteResult::class);
        $matchedRouteName = $routeResult->getMatchedRouteName();
        $allowedRoles     = [];

        assertIsString($matchedRouteName);
        foreach (array_keys($this->rules) as $routeRule) {
            assertIsString($routeRule);
            if (fnmatch($routeRule, $matchedRouteName, FNM_CASEFOLD)) {
                assertIsArray($this->rules[$routeRule]);
                $allowedRoles = $this->rules[$routeRule];
                break;
            }
        }

        if (count($allowedRoles) === 0) {
            return $this->protectionPolicy === GuardInterface::POLICY_ALLOW;
        }

        if (in_array('*', $allowedRoles)) {
            return true;
        }

        /** @var UserInterface $identity */
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
