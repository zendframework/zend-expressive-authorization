<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization\Adapter;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\Exception;
use Zend\Expressive\Router\RouteResult;
use Zend\Permissions\Acl\Acl;

class ZendAcl implements AuthorizationInterface
{
    /**
     * @var Acl
     */
    private $acl;

    public function __construct(Acl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * {@inheritDoc}
     */
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

        return $this->acl->isAllowed($role, $routeName);
    }
}
