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
use Zend\Permissions\Rbac\AssertionInterface;
use Zend\Permissions\Rbac\Rbac;

class ZendRbac implements AuthorizationInterface
{
    /**
     * @var Rbac
     */
    private $rbac;

    /**
     * @var AssertionInterface
     */
    private $assertion;

    /**
     * Constructor
     *
     * @param Rbac $rbac
     * @param ZendRbacAssertionInterface $assertion
     */
    public function __construct(Rbac $rbac, ZendRbacAssertionInterface $assertion = null)
    {
        $this->rbac = $rbac;
        $this->assertion = $assertion;
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
        if (null !== $this->assertion) {
            $this->assertion->setRequest($request);
        }
        return $this->rbac->isGranted($role, $routeName, $this->assertion);
    }
}
