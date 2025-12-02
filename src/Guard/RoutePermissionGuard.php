<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Service\AuthorizationServiceInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Override;
use Psr\Http\Message\ServerRequestInterface;

use function array_keys;
use function assert;
use function fnmatch;
use function in_array;
use function is_array;
use function is_string;

use const FNM_CASEFOLD;

/**
 * @final
 */
class RoutePermissionGuard extends AbstractGuard
{
    use ProtectionPolicyTrait;

    protected array $rules = [];

    public function __construct(
        private readonly AuthorizationServiceInterface $authorizationService,
        array $rules = [],
        string $protectionPolicy = GuardInterface::POLICY_ALLOW
    ) {
        $this->setProtectionPolicy($protectionPolicy);
        $this->rules = $rules;
    }

    #[Override]
    public function isGranted(ServerRequestInterface $request): bool
    {
        /** @var RouteResult $routeResult */
        $routeResult        = $request->getAttribute(RouteResult::class);
        $matchedRouteName   = $routeResult->getMatchedRouteName();
        $allowedPermissions = [];

        assert(is_string($matchedRouteName));
        foreach (array_keys($this->rules) as $routeRule) {
            assert(is_string($routeRule));
            if (fnmatch($routeRule, $matchedRouteName, FNM_CASEFOLD)) {
                assert(is_array($this->rules[$routeRule]));
                $allowedPermissions = $this->rules[$routeRule];
                break;
            }
        }

        if (empty($allowedPermissions)) {
            return $this->protectionPolicy === GuardInterface::POLICY_ALLOW;
        }

        if (in_array('*', $allowedPermissions)) {
            return true;
        }

        /** @var UserInterface $identity */
        $identity = $request->getAttribute(UserInterface::class);

        /** @var array $permissions */
        $permissions = $allowedPermissions['permissions'] ?? $allowedPermissions;
        /** @var string $condition */
        $condition = $allowedPermissions['condition'] ?? GuardInterface::CONDITION_AND;

        if (GuardInterface::CONDITION_AND === $condition) {
            /** @var string $permission */
            foreach ($permissions as $permission) {
                if (! $this->authorizationService->isGranted($identity, $permission)) {
                    return false;
                }
            }
            return true;
        }

        if (GuardInterface::CONDITION_OR === $condition) {
            /** @var string $permission */
            foreach ($permissions as $permission) {
                if ($this->authorizationService->isGranted($identity, $permission)) {
                    return true;
                }
            }
            return false;
        }
        // failsafe
        return $this->protectionPolicy === GuardInterface::POLICY_ALLOW;
    }
}
