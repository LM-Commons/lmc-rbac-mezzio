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

namespace LmcTest\Rbac\Mezzio\Options;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\OptionsFactory;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

#[CoversClass(OptionsFactory::class)]
class OptionsFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function testFactory(): void
    {
        $config = ['lmc_rbac' => []];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $config);

        $factory = new OptionsFactory();
        $options = $factory($serviceManager, Options::class);

        $this->assertEquals(0, count($options->getGuards()));
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $options->getProtectionPolicy());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $options->getRedirectStrategy());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $options->getUnauthorizedStrategy());
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function testFactoryNotCreatedException(): void
    {
        $config = [];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $config);

        $this->expectException(ServiceNotCreatedException::class);
        $factory = new OptionsFactory();
        $options = $factory($serviceManager, Options::class);
    }
}
