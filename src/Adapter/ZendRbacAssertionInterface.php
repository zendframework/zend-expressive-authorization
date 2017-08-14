<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authorization for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authorization/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Expressive\Authorization\Adapter;

use Zend\Permissions\Rbac\AssertionInterface as AssertionInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ZendRbacAssertionInterface extends AssertionInterface
{
    public function setRequest(ServerRequestInterface $request): void;
}
