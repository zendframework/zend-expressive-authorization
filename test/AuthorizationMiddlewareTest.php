<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Zend\Permissions\Rbac\Rbac;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Authorization\MiddlewareAssertionInterface;

class AuthorizationMiddlewareTest extends TestCase
{
    protected function setUp()
    {
        $this->rbac = $this->prophesize(Rbac::class);
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
    }

    public function testProcessWithourRouteResult()
    {
    }

    public function testProcessWithoutAssertion()
    {
    }

    public function testProcessWithAssertion()
    {
    }

    public function testProcessWithNoGrant()
    {
    }
}
