<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization\Adapter;

use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

class ZendPermissionsRbac implements AuthorizationInterface
{
    public function __construct(Rbac $rbac, ZendRbacAssertionInterface $assertion = null)
    {
        $this->rbac = $rbac;
        $this->assertion = $assertion;
    }

    public function isGranted(string $role, ServerRequestInterface $request): bool
    {
        $routeResult = $request->getAttribute(RouteResult::class, false);
        if (false === $routeResult) {
            throw new Exception\RuntimeException(sprintf(
                "The %s attribute is missing in the request",
                RouteResult::class
            ));
        }
        $routeName = $routeResult->getMatchedRouteName();
        if (null !== $this->assertion) {
            $this->assertion->setRequest($request);
        }
        return $this->rbac->isGranted($role, $routeName, $this->assertion);
    }

    public function getRoleAttributeName(): string
    {
        return 'USER_ROLE';
    }
}
