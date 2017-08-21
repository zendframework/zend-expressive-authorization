<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Authorization\AuthorizationInterface;

class AuthorizationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : AuthorizationMiddleware
    {
        $authorization = $container->has(AuthorizationInterface::class) ?
                         $container->get(AuthorizationInterface::class) :
                         null;
        if (null === $authorization) {
            throw new Exception\InvalidConfigException(
                'AuthorizationInterface service is missing'
            );
        }
        return new AuthorizationMiddleware($authorization);
    }
}
