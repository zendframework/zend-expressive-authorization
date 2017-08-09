<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization;

use Psr\Container\ContainerInterface;
use Zend\Permissions\Rbac\Rbac;

class AuthorizationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : AuthorizationMiddleware
    {
        $rbac = $container->get(Rbac::class);
        $assertion = $container->has(MiddlewareAssertionInterface::class)
            ? $container->get(MiddlewareAssertionInterface::class)
            : null;

        return new AuthorizationMiddleware($rbac, $assertion);
    }
}
