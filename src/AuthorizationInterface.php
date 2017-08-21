<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization;

use Psr\Http\Message\ServerRequestInterface;

interface AuthorizationInterface
{
    /**
     * Check if a role is granted for the request
     *
     * @param string $role
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isGranted(string $role, ServerRequestInterface $request): bool;
}
