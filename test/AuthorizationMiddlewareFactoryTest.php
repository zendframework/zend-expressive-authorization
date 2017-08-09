<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Zend\Permissions\Rbac\Rbac;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Authorization\AuthorizationMiddlewareFactory;
use Zend\Expressive\Authorization\MiddlewareAssertionInterface;

class AuthorizationMiddlewareFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new AuthorizationMiddlewareFactory();
        $this->rbac = $this->prophesize(Rbac::class);

        $this->container->get(Rbac::class)->willReturn($this->rbac->reveal());
    }

    public function testFactoryWithoutAssertion()
    {
        $this->container->has(MiddlewareAssertionInterface::class)->willReturn(false);

        $middleware = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testFactoryWithAssertion()
    {
        $assertion = $this->prophesize(MiddlewareAssertionInterface::class);
        $this->container->get(MiddlewareAssertionInterface::class)
                        ->willReturn($assertion->reveal());
        $this->container->has(MiddlewareAssertionInterface::class)->willReturn(true);

        $middleware = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }
}
