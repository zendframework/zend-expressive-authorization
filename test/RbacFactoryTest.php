<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authorization\RbacFactory;
use Zend\Permissions\Rbac\Rbac;

class RbacFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->factory = new RbacFactory();
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithEmptyConfig()
    {
        $this->container->get('config')->willReturn(null);

        $rbac = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithoutPermissionsConfig()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => []
            ]
        ]);

        $rbac = ($this->factory)($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithoutRolesConfig()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'permissions' => []
            ]
        ]);

        $rbac = ($this->factory)($this->container->reveal());
    }

    public function testFactoryWithEmptyRolesAndPermissionsConfig()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => [],
                'permissions' => []
            ]
        ]);

        $rbac = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(Rbac::class, $rbac);
    }

    public function testFactoryWithAuthorizationConfig()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => [ 'foo' => [] ],
                'permissions' => [
                    'foo' => [ 'route1' ]
                ]
            ]
        ]);

        $rbac = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(Rbac::class, $rbac);
        $this->assertTrue($rbac->hasRole('foo'));
        $this->assertTrue($rbac->isGranted('foo', 'route1'));
        $this->assertFalse($rbac->isGranted('foo', 'route2'));
    }

    public function testFactoryWithMissingRoleInParentConfig()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => [ 'foo' => [ 'bar' ] ],
                'permissions' => [
                    'foo' => [ 'route1' ]
                ]
            ]
        ]);

        $rbac = ($this->factory)($this->container->reveal());
        $this->assertInstanceOf(Rbac::class, $rbac);
        $this->assertTrue($rbac->hasRole('foo'));
        $this->assertTrue($rbac->isGranted('foo', 'route1'));
        $this->assertFalse($rbac->isGranted('foo', 'route2'));
        $this->assertTrue($rbac->hasRole('bar'));
        $this->assertTrue($rbac->isGranted('bar', 'route1'));
        $this->assertFalse($rbac->isGranted('bar', 'route2'));
    }
}
