<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Zend\Expressive\Authorization\Adapter\ZendAcl;
use Zend\Expressive\Authorization\Adapter\ZendAclFactory;

class ZendAclFactoryTest extends TestCase
{
    protected function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithoutConfig()
    {
        $this->container->get('config')->willReturn([]);

        $factory = new ZendAclFactory();
        $factory($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithoutZendAclConfig()
    {
        $this->container->get('config')->willReturn(['authorization' => []]);

        $factory = new ZendAclFactory();
        $factory($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithoutResources()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => []
            ]
        ]);

        $factory = new ZendAclFactory();
        $factory($this->container->reveal());
    }

    public function testFactoryWithEmptyRolesResources()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => [],
                'resources' => []
            ]
        ]);

        $factory = new ZendAclFactory();
        $zendAcl = $factory($this->container->reveal());
        $this->assertInstanceOf(ZendAcl::class, $zendAcl);
    }

    public function testFactoryWithoutAllowOrDeny()
    {
        $config = [
            'authorization' => [
                'roles' => [
                    'admini'      => [],
                    'editor'      => ['administrator'],
                    'contributor' => ['editor'],
                ],
                'resources' => [
                    'admin.dashboard',
                    'admin.posts',
                    'admin.publish',
                    'admin.settings',
                ]
            ]
        ];
        $this->container->get('config')->willReturn($config);

        $factory = new ZendAclFactory();
        $zendAcl = $factory($this->container->reveal());
        $this->assertInstanceOf(ZendAcl::class, $zendAcl);
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithInvalidRole()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => [
                    1 => [],
                ],
                'permissions' => []
            ]
        ]);

        $factory = new ZendAclFactory();
        $zendAcl = $factory($this->container->reveal());
    }

    /**
     * @expectedException Zend\Expressive\Authorization\Exception\InvalidConfigException
     */
    public function testFactoryWithUnknownRole()
    {
        $this->container->get('config')->willReturn([
            'authorization' => [
                'roles' => [
                    'administrator' => [],
                ],
                'resources' => [
                    'admin.dashboard',
                    'admin.posts',
                ],
                'allow' => [
                    'editor' => ['admin.dashboard']
                ]
            ]
        ]);

        $factory = new ZendAclFactory();
        $zendAcl = $factory($this->container->reveal());
    }
}
