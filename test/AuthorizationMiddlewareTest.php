<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Authorization;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Router\RouteResult;

class AuthorizationMiddlewareTest extends TestCase
{

    protected function setUp()
    {
        $this->authorization = $this->prophesize(AuthorizationInterface::class);
        $this->request = $this->prophesize(Request::class);
        $this->delegate = $this->prophesize(DelegateInterface::class);
        $this->response = $this->prophesize(ResponseInterface::class);
    }

    public function testConstructor()
    {
        $middleware = new AuthorizationMiddleware($this->authorization->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testProcessWithoutRoleAttribute()
    {
        $this->authorization->getRoleAttributeName()->willReturn('foo');
        $this->request->getAttribute('foo', false)->willReturn(false);
        $middleware = new AuthorizationMiddleware($this->authorization->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testProcessRoleNotGranted()
    {
        $this->authorization->getRoleAttributeName()->willReturn('foo');
        $this->request->getAttribute('foo', false)->willReturn('foo');
        $this->authorization->isGranted('foo', $this->request->reveal())->willReturn(false);

        $middleware = new AuthorizationMiddleware($this->authorization->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testProcessRoleGranted()
    {
        $this->authorization->getRoleAttributeName()->willReturn('foo');
        $this->request->getAttribute('foo', false)->willReturn('foo');
        $this->authorization->isGranted('foo', $this->request->reveal())->willReturn(true);

        $middleware = new AuthorizationMiddleware($this->authorization->reveal());
        $this->delegate->process(Argument::any())->willReturn($this->response->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
