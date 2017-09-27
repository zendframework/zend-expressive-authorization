<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthorizationMiddleware implements ServerMiddlewareInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var ResponseInterface
     */
    private $responsePrototype;

    /**
     * Constructor
     *
     * @param Rbac $rbac
     * @param MiddlewareAssertionInterface $assertion
     * @return void
     */
    public function __construct(AuthorizationInterface $authorization, ResponseInterface $responsePrototype)
    {
        $this->authorization = $authorization;
        $this->responsePrototype = $responsePrototype;
    }

    /**
     * {@inheritDoc}
     * @todo Use role/identity interface from zend-expressive-authentication once published.
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $role = $request->getAttribute(AuthorizationInterface::class, false);

        if (false === $role) {
            return $this->responsePrototype->withStatus(401);
        }

        return $this->authorization->isGranted($role, $request)
            ? $delegate->process($request)
            : $this->responsePrototype->withStatus(403);
    }
}
