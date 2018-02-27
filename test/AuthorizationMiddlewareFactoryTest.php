<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionProperty;
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Authorization\AuthorizationMiddlewareFactory;
use Zend\Expressive\Authorization\Exception;

class AuthorizationMiddlewareFactoryTest extends TestCase
{
    /** @var ContainerInterface|ObjectProphecy */
    private $container;

    /** @var AuthorizationMiddlewareFactory */
    private $factory;

    /** @var AuthorizationInterface|ObjectProphecy */
    private $authorization;

    /** @var ResponseInterface|ObjectProphecy */
    private $responsePrototype;

    /** @var callable */
    private $responseFactory;

    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new AuthorizationMiddlewareFactory();
        $this->authorization = $this->prophesize(AuthorizationInterface::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
        $this->responseFactory = function () {
            return $this->responsePrototype->reveal();
        };

        $this->container
            ->get(AuthorizationInterface::class)
            ->will([$this->authorization, 'reveal']);
        $this->container
            ->get(ResponseInterface::class)
            ->willReturn($this->responseFactory);
    }

    public function testFactoryWithoutAuthorization()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(false);

        $this->expectException(Exception\InvalidConfigException::class);
        ($this->factory)($this->container->reveal());
    }

    public function testFactory()
    {
        $this->container->has(AuthorizationInterface::class)->willReturn(true);
        $this->container->has(ResponseInterface::class)->willReturn(true);

        $middleware = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
        $this->assertResponseFactoryReturns($this->responsePrototype->reveal(), $middleware);
    }

    public static function assertResponseFactoryReturns(
        ResponseInterface $expected,
        AuthorizationMiddleware $middleware
    ) : void {
        $r = new ReflectionProperty($middleware, 'responseFactory');
        $r->setAccessible(true);
        $responseFactory = $r->getValue($middleware);
        Assert::assertSame($expected, $responseFactory());
    }
}
