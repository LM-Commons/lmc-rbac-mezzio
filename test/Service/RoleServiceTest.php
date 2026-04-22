<?php

declare(strict_types=1);

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace LmcTest\Rbac\Mezzio\Service;

use Laminas\Permissions\Rbac\Role;
use Lmc\Rbac\Identity\IdentityInterface;
use Lmc\Rbac\Mezzio\Role\RecursiveRoleIteratorStrategy;
use Lmc\Rbac\Mezzio\Service\RoleService;
use Lmc\Rbac\Role\InMemoryRoleProvider;
use Lmc\Rbac\Service\RoleService as BaseRoleService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(RoleService::class)]
final class RoleServiceTest extends TestCase
{
    /**
     * @return iterable<array-key, array<array-key, mixed>>
     */
    public static function roleProvider(): array
    {
        return [
            // No identity role
            [
                'rolesConfig'   => [],
                'identityRoles' => [],
                'rolesToCheck'  => [
                    'member',
                ],
                'doesMatch'     => false,
            ],

            // Simple
            [
                'rolesConfig'   => [
                    'member' => [
                        'children' => ['guest'],
                    ],
                    'guest',
                ],
                'identityRoles' => [
                    'guest',
                ],
                'rolesToCheck'  => [
                    'member',
                ],
                'doesMatch'     => false,
            ],
            [
                'rolesConfig'   => [
                    'member' => [
                        'children' => ['guest'],
                    ],
                    'guest',
                ],
                'identityRoles' => [
                    'member',
                ],
                'rolesToCheck'  => [
                    'member',
                ],
                'doesMatch'     => true,
            ],

            // Complex role inheritance
            [
                'rolesConfig'   => [
                    'admin'     => [
                        'children' => ['moderator'],
                    ],
                    'moderator' => [
                        'children' => ['member'],
                    ],
                    'member'    => [
                        'children' => ['guest'],
                    ],
                    'guest',
                ],
                'identityRoles' => [
                    'member',
                    'moderator',
                ],
                'rolesToCheck'  => [
                    'admin',
                ],
                'doesMatch'     => false,
            ],
            [
                'rolesConfig'   => [
                    'admin'     => [
                        'children' => ['moderator'],
                    ],
                    'moderator' => [
                        'children' => ['member'],
                    ],
                    'member'    => [
                        'children' => ['guest'],
                    ],
                    'guest',
                ],
                'identityRoles' => [
                    'member',
                    'admin',
                ],
                'rolesToCheck'  => [
                    'moderator',
                ],
                'doesMatch'     => true,
            ],

            // Complex role inheritance and multiple check
            [
                'rolesConfig'   => [
                    'sysadmin' => [
                        'children' => ['siteadmin', 'admin'],
                    ],
                    'siteadmin',
                    'admin'     => [
                        'children' => ['moderator'],
                    ],
                    'moderator' => [
                        'children' => ['member'],
                    ],
                    'member'    => [
                        'children' => ['guest'],
                    ],
                    'guest',
                ],
                'identityRoles' => [
                    'member',
                    'moderator',
                ],
                'rolesToCheck'  => [
                    'admin',
                    'sysadmin',
                ],
                'doesMatch'     => false,
            ],
            [
                'rolesConfig'   => [
                    'sysadmin' => [
                        'children' => ['siteadmin', 'admin'],
                    ],
                    'siteadmin',
                    'admin'     => [
                        'children' => ['moderator'],
                    ],
                    'moderator' => [
                        'children' => ['member'],
                    ],
                    'member'    => [
                        'children' => ['guest'],
                    ],
                    'guest',
                ],
                'identityRoles' => [
                    'moderator',
                    'admin',
                ],
                'rolesToCheck'  => [
                    'sysadmin',
                    'siteadmin',
                    'member',
                ],
                'doesMatch'     => true,
            ],
            // With Role objects
            [
                'rolesConfig'   => [
                    'member',
                    'guest',
                ],
                'identityRoles' => [
                    'member',
                ],
                'rolesToCheck'  => [
                    new Role('member'),
                ],
                'doesMatch'     => true,
            ],
        ];
    }

    public function testGetIdentityRoles(): void
    {
        $baseRoleProvider = $this->createMock(BaseRoleService::class);
        $baseRoleProvider->expects($this->once())->method('getIdentityRoles')->willReturn([]);
        $roleService = new RoleService($baseRoleProvider, new RecursiveRoleIteratorStrategy());
        self::assertIsArray($roleService->getIdentityRoles());
    }

    #[DataProvider('roleProvider')]
    public function testMatchIdentityRoles(
        array $rolesConfig,
        array $identityRoles,
        array $rolesToCheck,
        bool $doesMatch
    ): void {
        $identity = $this->createStub(IdentityInterface::class);
        $identity->method('getRoles')->willReturn($identityRoles);

        $roleProvider    = new InMemoryRoleProvider($rolesConfig);
        $baseRoleService = new BaseRoleService($roleProvider, 'guest');

        $roleService = new RoleService($baseRoleService, new RecursiveRoleIteratorStrategy());

        $this->assertEquals($doesMatch, $roleService->matchIdentityRoles($identity, $rolesToCheck));
    }
}
