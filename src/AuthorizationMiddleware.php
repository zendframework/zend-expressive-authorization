<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Authorization\Exception;
use Zend\Expressive\Router\RouteResult;
use Zend\Permissions\Rbac\Rbac;

class AuthorizationMiddleware implements ServerMiddlewareInterface
{
    /**
     * @var MiddlewareAssertionInterface
     */
    protected $assertion;

    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * Constructor
     *
     * @param Rbac $rbac
     * @param MiddlewareAssertionInterface $assertion
     * @return void
     */
    public function __construct(Rbac $rbac, MiddlewareAssertionInterface $assertion = null)
    {
        $this->rbac      = $rbac;
        $this->assertion = $assertion;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $role = $request->getAttribute('USER_ROLE', false);
        if (false === $role) {
            return new EmptyResponse(401);
        }
        // dynamic assertion if present
        if (null !== $this->assertion) {
            $this->assertion->setUser($request->getAttribute('USER', false));
            $this->assertion->setRequest($request);
        }

        $routeResult = $request->getAttribute(RouteResult::class, false);
        if (false === $routeResult) {
            throw new Exception\RuntimeException(sprintf(
                "The %s attribute is missing in the request",
                RouteResult::class
            ));
        }
        $routeName = $routeResult->getMatchedRouteName();
        if (! $this->rbac->isGranted($role, $routeName, $this->assertion)) {
            return new EmptyResponse(403);
        }

        return $delegate->process($request);
    }
}
