<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Authentication\DefaultUser;
use Zend\Expressive\Authentication\UserInterface;
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\AuthorizationMiddleware;

class AuthorizationMiddlewareTest extends TestCase
{
    /** @var AuthorizationInterface|ObjectProphecy */
    private $authorization;

    /** @var ServerRequestInterface|ObjectProphecy */
    private $request;

    /** @var RequestHandlerInterface|ObjectProphecy */
    private $handler;

    /** @var ResponseInterface|ObjectProphecy */
    private $responsePrototype;

    /** @var callable */
    private $responseFactory;

    protected function setUp()
    {
        $this->authorization = $this->prophesize(AuthorizationInterface::class);
        $this->request = $this->prophesize(ServerRequestInterface::class);
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
        $this->responsePrototype = $this->prophesize(ResponseInterface::class);
        $this->responseFactory = function () {
            return $this->responsePrototype->reveal();
        };
    }

    public function testConstructor()
    {
        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testProcessWithoutUserAttribute()
    {
        $this->request->getAttribute(UserInterface::class, false)->willReturn(false);
        $this->responsePrototype->withStatus(401)->will([$this->responsePrototype, 'reveal']);

        $this->handler
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->responsePrototype->reveal(), $response);
    }

    public function testProcessRoleNotGranted()
    {
        $this->request
            ->getAttribute(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));
        $this->responsePrototype
            ->withStatus(403)
            ->will([$this->responsePrototype, 'reveal']);
        $this->authorization
            ->isGranted('bar', Argument::that([$this->request, 'reveal']))
            ->willReturn(false);

        $this->handler
            ->handle(Argument::any())
            ->shouldNotBeCalled();

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->responsePrototype->reveal(), $response);
    }

    public function testProcessRoleGranted()
    {
        $this->request
            ->getAttribute(UserInterface::class, false)
            ->willReturn($this->generateUser('foo', ['bar']));
        $this->authorization
            ->isGranted('bar', Argument::that([$this->request, 'reveal']))
            ->willReturn(true);

        $this->handler
            ->handle(Argument::any())
            ->will([$this->responsePrototype, 'reveal']);

        $middleware = new AuthorizationMiddleware($this->authorization->reveal(), $this->responseFactory);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->handler->reveal()
        );

        $this->assertSame($this->responsePrototype->reveal(), $response);
    }

    private function generateUser(string $identity, array $roles = []) : DefaultUser
    {
        return new DefaultUser($identity, $roles);
    }
}
