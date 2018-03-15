<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace ZendTest\Expressive\Authorization;

use PHPUnit\Framework\TestCase;
use Zend\Expressive\Authorization\AuthorizationMiddleware;
use Zend\Expressive\Authorization\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    /** @var ConfigProvider */
    private $provider;

    protected function setUp()
    {
        $this->provider = new ConfigProvider();
    }

    public function testProviderDefinesExpectedFactoryServices()
    {
        $config = $this->provider->getDependencies();
        $factories = $config['factories'];

        $this->assertArrayHasKey(AuthorizationMiddleware::class, $factories);
    }

    public function testInvocationReturnsArrayWithDependencies()
    {
        $config = ($this->provider)();

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('authorization', $config);
        $this->assertInternalType('array', $config['authorization']);

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertInternalType('array', $config['dependencies']);
        $this->assertArrayHasKey('aliases', $config['dependencies']);
        $this->assertArrayHasKey('factories', $config['dependencies']);
    }
}
