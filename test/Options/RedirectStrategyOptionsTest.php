<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Options;

use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RedirectStrategyOptions::class)]
final class RedirectStrategyOptionsTest extends TestCase
{
    public function testAssertDefaultValues(): void
    {
        $redirectStrategyOptions = new RedirectStrategyOptions();

        $this->assertTrue($redirectStrategyOptions->getRedirectWhenConnected());
        $this->assertEquals('login', $redirectStrategyOptions->getRedirectToRouteDisconnected());
        $this->assertEquals('home', $redirectStrategyOptions->getRedirectToRouteConnected());
        $this->assertTrue($redirectStrategyOptions->getAppendPreviousUri());
        $this->assertEquals('redirectTo', $redirectStrategyOptions->getPreviousUriQueryKey());
    }

    public function testSettersAndGetters(): void
    {
        $redirectStrategyOptions = new RedirectStrategyOptions([
            'redirect_when_connected'        => false,
            'redirect_to_route_connected'    => 'foo',
            'redirect_to_route_disconnected' => 'bar',
            'append_previous_uri'            => false,
            'previous_uri_query_key'         => 'redirect-to',
        ]);

        $this->assertFalse($redirectStrategyOptions->getRedirectWhenConnected());
        $this->assertEquals('foo', $redirectStrategyOptions->getRedirectToRouteConnected());
        $this->assertEquals('bar', $redirectStrategyOptions->getRedirectToRouteDisconnected());
        $this->assertFalse($redirectStrategyOptions->getAppendPreviousUri());
        $this->assertEquals('redirect-to', $redirectStrategyOptions->getPreviousUriQueryKey());
    }
}
