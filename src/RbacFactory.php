<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization;

use Psr\Container\ContainerInterface;
use Zend\Permissions\Rbac\Rbac;

class RbacFactory
{
    public function __invoke(ContainerInterface $container) : Rbac
    {
        $config = $container->get('config')['authorization'] ?? null;
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
            $rbac->addRole($role, $parents);
        }
        // Permissions
        foreach ($config['permissions'] as $role => $permissions) {
            foreach ($permissions as $perm) {
                $rbac->getRole($role)->addPermission($perm);
            }
        }

        return $rbac;
    }
}
