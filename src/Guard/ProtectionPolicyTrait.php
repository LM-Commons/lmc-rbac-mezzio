<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

/**
 * Trait that is can be used for any guard that uses the protection policy pattern
 */
trait ProtectionPolicyTrait
{
    protected string $protectionPolicy = GuardInterface::POLICY_DENY;

    /**
     * Set the protection policy
     */
    public function setProtectionPolicy(string $protectionPolicy): void
    {
        $this->protectionPolicy = $protectionPolicy;
    }

    /**
     * Get the protection policy
     */
    public function getProtectionPolicy(): string
    {
        return $this->protectionPolicy;
    }
}
