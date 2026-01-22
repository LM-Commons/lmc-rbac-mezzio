<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Options;

use Laminas\Stdlib\AbstractOptions;
use Laminas\Stdlib\ArrayUtils;
use Lmc\Rbac\Mezzio\Exception\InvalidProtectionPolicyException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Guard\RouteGuardFactory;
use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuard;
use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuardFactory;

use function sprintf;

/**
 * @template TValue
 * @extends AbstractOptions<TValue>
 */
class Options extends AbstractOptions
{
    // phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore,WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCapsProperty
    /** @var bool $__strictMode__ */
    protected $__strictMode__ = false;
    // phpcs: enable

    /** @var array<string, TValue> */
    protected array $guards = [];

    /**
     * Protection policy for guards (can be "deny" or "allow")
     */
    protected string $protectionPolicy = GuardInterface::POLICY_ALLOW;

    /**
     * Options for the unauthorized strategy
     */
    protected ?UnauthorizedStrategyOptions $unauthorizedStrategyOptions = null;

    /**
     * Options for the redirect strategy
     */
    protected ?RedirectStrategyOptions $redirectStrategyOptions = null;

    protected array $strategies = [];

    protected array $guardManager = [
        'factories' => [
            RouteGuard::class           => RouteGuardFactory::class,
            RoutePermissionGuard::class => RoutePermissionGuardFactory::class,
        ],
    ];

    protected array $exceptionCodes = ['403'];

    /**
     * @return array<string, TValue>
     */
    public function getGuards(): array
    {
        return $this->guards;
    }

    /**
     * @param array<string, mixed> $guards
     */
    public function setGuards(array $guards): void
    {
        $this->guards = $guards;
    }

    public function getProtectionPolicy(): string
    {
        return $this->protectionPolicy;
    }

    public function setProtectionPolicy(string $protectionPolicy): void
    {
        if ($protectionPolicy !== GuardInterface::POLICY_ALLOW && $protectionPolicy !== GuardInterface::POLICY_DENY) {
            throw new InvalidProtectionPolicyException(sprintf(
                'An invalid protection policy was set. Can only be "deny" or "allow", "%s" given',
                $protectionPolicy
            ));
        }
        $this->protectionPolicy = $protectionPolicy;
    }

    /**
     * @param iterable<string, TValue> $unauthorizedStrategyOptions
     */
    public function setUnauthorizedStrategyOptions(array $unauthorizedStrategyOptions): void
    {
        $this->unauthorizedStrategyOptions = new UnauthorizedStrategyOptions($unauthorizedStrategyOptions);
    }

    /**
     * Get the unauthorized strategy options
     */
    public function getUnauthorizedStrategyOptions(): ?UnauthorizedStrategyOptions
    {
        if (null === $this->unauthorizedStrategyOptions) {
            $this->unauthorizedStrategyOptions = new UnauthorizedStrategyOptions();
        }

        return $this->unauthorizedStrategyOptions;
    }

    /**
     * Set the redirect strategy options
     *
     * @param iterable<string, TValue> $redirectStrategyOptions
     */
    public function setRedirectStrategyOptions(array $redirectStrategyOptions): void
    {
        $this->redirectStrategyOptions = new RedirectStrategyOptions($redirectStrategyOptions);
    }

    /**
     * Get the redirect strategy options
     */
    public function getRedirectStrategyOptions(): ?RedirectStrategyOptions
    {
        if (null === $this->redirectStrategyOptions) {
            $this->redirectStrategyOptions = new RedirectStrategyOptions();
        }

        return $this->redirectStrategyOptions;
    }

    public function setStrategies(array $strategies): void
    {
        $this->strategies = $strategies;
    }

    public function getStrategies(): array
    {
        return $this->strategies;
    }

    public function setGuardManager(array $guardManager): void
    {
        $this->guardManager = ArrayUtils::merge($this->guardManager, $guardManager);
    }

    public function getGuardManager(): array
    {
        return $this->guardManager;
    }

    public function getExceptionCodes(): array
    {
        return $this->exceptionCodes;
    }

    public function setExceptionCodes(array $exceptionCodes): void
    {
        $this->exceptionCodes = $exceptionCodes;
    }

    public function getGuardOptions(string $guard): mixed
    {
        return $this->guards[$guard] ?? [];
    }
}
