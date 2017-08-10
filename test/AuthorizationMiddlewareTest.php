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
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Authorization\MiddlewareAssertionInterface;
use Zend\Expressive\Router\RouteResult;
use Zend\Permissions\Rbac\Rbac;


class AuthorizationMiddlewareTest extends TestCase
{

    protected function setUp()
    {
        $this->rbac = $this->prophesize(Rbac::class);
        $this->request = $this->prophesize(Request::class);
        $this->delegate = $this->prophesize(DelegateInterface::class);
    }

    public function testConstructorWithoutAssertion()
    {
        $middleware = new AuthorizationMiddleware($this->rbac->reveal());
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testConstructorWithAssertion()
    {
        $assertion = $this->prophesize(MiddlewareAssertionInterface::class);

        $middleware = new AuthorizationMiddleware(
            $this->rbac->reveal(),
            $assertion->reveal()
        );
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    public function testProcessWithoutUserRole()
    {
        $middleware = new AuthorizationMiddleware($this->rbac->reveal());

        $this->request->getAttribute('USER_ROLE', false)->willReturn(false);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );

        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\RuntimeException
     */
    public function testProcessWithoutRouteResult()
    {
        $middleware = new AuthorizationMiddleware($this->rbac->reveal());

        $this->request->getAttribute('USER_ROLE', false)->willReturn(true);
        $this->request->getAttribute(RouteResult::class, false)->willReturn(false);

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );
    }

    public function testProcessGrantWithoutAssertion()
    {
        $this->rbac->isGranted('user', 'home', null)->willReturn(true);
        $this->delegate->process(Argument::any())->willReturn(true);

        $middleware = new AuthorizationMiddleware($this->rbac->reveal());

        $this->request->getAttribute('USER_ROLE', false)->willReturn('user');

        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->getMatchedRouteName()->willReturn('home');

        $this->request->getAttribute(RouteResult::class, false)
                      ->willReturn($routeResult->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );
        $this->assertTrue($response);
    }

    public function testProcessNoGrantWithoutAssertion()
    {
        $this->rbac->isGranted('user', 'home', null)->willReturn(false);
        $this->delegate->process(Argument::any())->willReturn(true);

        $middleware = new AuthorizationMiddleware($this->rbac->reveal());

        $this->request->getAttribute('USER_ROLE', false)->willReturn('user');

        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->getMatchedRouteName()->willReturn('home');

        $this->request->getAttribute(RouteResult::class, false)
                      ->willReturn($routeResult->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );
        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testProcessGrantWithAssertion()
    {
        $assertion = $this->prophesize(MiddlewareAssertionInterface::class);
        $this->rbac->isGranted('user', 'home', $assertion->reveal())->willReturn(true);
        $this->delegate->process(Argument::any())->willReturn(true);

        $middleware = new AuthorizationMiddleware(
            $this->rbac->reveal(),
            $assertion->reveal()
        );

        $this->request->getAttribute('USER_ROLE', false)->willReturn('user');
        $this->request->getAttribute('USER', false)->willReturn(false);

        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->getMatchedRouteName()->willReturn('home');

        $this->request->getAttribute(RouteResult::class, false)
                      ->willReturn($routeResult->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );
        $this->assertTrue($response);
        $assertion->setUser(false)->shouldHaveBeenCalled();
        $assertion->setRequest($this->request->reveal())->shouldHaveBeenCalled();
    }

    public function testProcessNoGrantWithAssertion()
    {
        $assertion = $this->prophesize(MiddlewareAssertionInterface::class);
        $this->rbac->isGranted('user', 'home', $assertion->reveal())->willReturn(false);
        $this->delegate->process(Argument::any())->willReturn(true);

        $middleware = new AuthorizationMiddleware(
            $this->rbac->reveal(),
            $assertion->reveal()
        );

        $this->request->getAttribute('USER_ROLE', false)->willReturn('user');
        $this->request->getAttribute('USER', false)->willReturn(false);

        $routeResult = $this->prophesize(RouteResult::class);
        $routeResult->getMatchedRouteName()->willReturn('home');

        $this->request->getAttribute(RouteResult::class, false)
                      ->willReturn($routeResult->reveal());

        $response = $middleware->process(
            $this->request->reveal(),
            $this->delegate->reveal()
        );
        $this->assertInstanceOf(EmptyResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $assertion->setUser(false)->shouldHaveBeenCalled();
        $assertion->setRequest($this->request->reveal())->shouldHaveBeenCalled();
    }
}
