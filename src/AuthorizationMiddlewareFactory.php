<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authorization;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Authentication\ResponsePrototypeTrait;

class AuthorizationMiddlewareFactory
{
    use ResponsePrototypeTrait;

    public function __invoke(ContainerInterface $container) : AuthorizationMiddleware
    {
        if (! $container->has(AuthorizationInterface::class)) {
            throw new Exception\InvalidConfigException(sprintf(
                'Cannot create %s service; dependency %s is missing',
                AuthorizationMiddleware::class,
                AuthorizationInterface::class
            ));
        }

        try {
            $responsePrototype = $this->getResponsePrototype($container);
        } catch (\Exception $e) {
            throw new Exception\InvalidConfigException($e->getMessage());
        }

        return new AuthorizationMiddleware(
            $container->get(AuthorizationInterface::class),
            $responsePrototype
        );
    }
}
