<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authorization;

class ConfigProvider
{
    /**
     * Return the configuration array.
     */
    public function __invoke() : array
    {
        return [
            'dependencies'  => $this->getDependencies(),
            'authorization' => $this->getAuthorizationConfig(),
        ];
    }

    /**
     * Returns the configuration for the AuthorizationInterface adapter
     */
    public function getAuthorizationConfig() : array
    {
        return [
            /**
             * Example using ZendAcl:
             *
             * 'roles' => [
             *     // insert the role with parent (if any)
             *     // e.g. 'editor' => ['admin'] (admin is parent of editor)
             * ],
             * 'resources' => [
             *     // an array of resources, as string
             * ],
             * 'allow' => [
             *     // for each role allow some resources
             *     // e.g. 'admin' => ['admin.pages']
             * ],
             * 'deny' => [
             *     // for each role deny some resources
             *     // e.g. 'admin' => ['admin.pages']
             * ],
             *
             * Example using ZendRbac:
             *
             * 'roles' => [
             *     // insert the role with parent (if any)
             *     // e.g. 'editor' => ['admin'] (admin is parent of editor)
             * ],
             * 'permissions' => [
             *     // for each role insert one or more permissions
             *     // e.g. 'admin' => ['admin.pages']
             * ],
             */
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'aliases' => [
                // Provide an alias for the AuthorizationInterface based on the adapter you are using.
                // AuthorizationInterface::class => ZendAcl::class,
                // AuthorizationInterface::class => ZendRbac::class,
            ],
            'factories' => [
                AuthorizationMiddleware::class => AuthorizationMiddlewareFactory::class,
            ],
        ];
    }
}
