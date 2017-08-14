<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Authorization\AuthorizationMiddlewareFactory;

class AuthorizationMiddlewareFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new AuthorizationMiddlewareFactory();
        $this->authorization = $this->prophesize(AuthorizationInterface::class);

        $this->container->get(AuthorizationInterface::class)
                        ->willReturn($this->authorization->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithoutAuthorization()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(false);

        $middleware = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithAuthorizationEmptyRole()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(true);
        $this->authorization->getRoleAttributeName()->willReturn('');

        $middleware = ($this->factory)($this->container->reveal());
    }

    public function testFactory()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(true);
        $this->authorization->getRoleAttributeName()->willReturn('foo');

        $middleware = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }
}
