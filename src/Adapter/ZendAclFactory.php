<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization\Adapter;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\Exception;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Exception\ExceptionInterface as AclExceptionInterface;

class ZendAclFactory
{
    public function __invoke(ContainerInterface $container): AuthorizationInterface
    {
        $config = $container->get('config')['authorization'] ?? null;
        if (null === $config) {
            throw new Exception\InvalidConfigException(
                'No authorization config provided'
            );
        }
        if (! isset($config['roles'])) {
            throw new Exception\InvalidConfigException(
                'No authorization roles configured for ZendAcl'
            );
        }
        if (! isset($config['resources'])) {
            throw new Exception\InvalidConfigException(
                'No authorization resources configured for ZendAcl'
            );
        }

        $acl = new Acl();
        // Roles
        foreach ($config['roles'] as $role => $parents) {
            foreach ($parents as $parent) {
                if (! $acl->hasRole($parent)) {
                    try {
                        $acl->addRole($parent);
                    } catch (AclExceptionInterface $e) {
                        throw new Exception\InvalidConfigException($e->getMessage());
                    }
                }
            }
            try {
                $acl->addRole($role, $parents);
            } catch (AclExceptionInterface $e) {
                throw new Exception\InvalidConfigException($e->getMessage());
            }
        }
        // Resources
        foreach ($config['resources'] as $resource) {
            try {
                $acl->addResource($resource);
            } catch (AclExceptionInterface $e) {
                throw new Exception\InvalidConfigException($e->getMessage());
            }
        }
        // Allow
        $allow = $config['allow'] ?? [];
        foreach ($allow as $role => $resources) {
            try {
                $acl->allow($role, $resources);
            } catch (AclExceptionInterface $e) {
                throw new Exception\InvalidConfigException($e->getMessage());
            }
        }
        // Deny
        $deny = $config['deny'] ?? [];
        foreach ($deny as $role => $resources) {
            try {
                $acl->deny($role, $resources);
            } catch (AclExceptionInterface $e) {
                throw new Exception\InvalidConfigException($e->getMessage());
            }
        }

        return new ZendAcl($acl);
    }
}
