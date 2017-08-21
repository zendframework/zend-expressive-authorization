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
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;
use Zend\Permissions\Rbac\Exception\ExceptionInterface as RbacExceptionInterface;

class ZendRbacFactory
{
    public function __invoke(ContainerInterface $container) : AuthorizationInterface
    {
        $config = $container->get('config')['authorization'] ?? null;
        if (null === $config) {
            throw new Exception\InvalidConfigException(
                'No authorization config provided'
            );
        }
        if (! isset($config['roles'])) {
            throw new Exception\InvalidConfigException(
                'No authorization roles configured'
            );
        }
        if (! isset($config['permissions'])) {
            throw new Exception\InvalidConfigException(
                'No authorization permissions configured'
            );
        }

        $rbac = new Rbac();
        $rbac->setCreateMissingRoles(true);
        // Roles and parents
        foreach ($config['roles'] as $role => $parents) {
            try {
                $rbac->addRole($role, $parents);
            } catch (RbacExceptionInterface $e) {
                throw new Exception\InvalidConfigException($e->getMessage());
            }
        }
        // Permissions
        foreach ($config['permissions'] as $role => $permissions) {
            foreach ($permissions as $perm) {
                try {
                    $rbac->getRole($role)->addPermission($perm);
                } catch (RbacExceptionInterface $e) {
                    throw new Exception\InvalidConfigException($e->getMessage());
                }
            }
        }

        $assertion = $container->has(ZendRbacAssertionInterface::class) ?
                     $container->get(ZendRbacAssertionInterface::class) :
                     null;

        return new ZendRbac($rbac, $assertion);
    }
}
