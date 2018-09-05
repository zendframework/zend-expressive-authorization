# Authorization adapters

You can configure the authorization adapter to use via your service container
configuration. Specifically, you can either map the service name
`Zend\Expressive\Authorization\AuthorizationInterface` to a factory, or alias it
to the appropriate service.

For instance, using [Expressive container configuration](https://docs.zendframework.com/zend-expressive/v3/features/container/config/),
you could select the zend-expressive-authorization-acl adapter in either of the
following ways:

- Using an alias:
  ```php
  use Zend\Expressive\Authorization\AuthorizationInterface;
  use Zend\Expressive\Authorization\Acl\ZendAcl;
  
  return [
      'dependencies' => [
          // Using an alias:
          'aliases' => [
              AuthorizationInterface::class => ZendAcl::class,
          ],
      ],
  ];
  ```

- Mapping to a factory:
  ```php
  use Zend\Expressive\Authorization\AuthorizationInterface;
  use Zend\Expressive\Authorization\Acl\ZendAclFactory;
  
  return [
      'dependencies' => [
          // Using an alias:
          'factories' => [
              AuthorizationInterface::class => ZendAclFactory::class,
          ],
      ],
  ];
  ```

We provide two different adapters.

- The RBAC adapter is provided by [zend-expressive-authorization-rbac](https://github.com/zendframework/zend-expressive-authorization-rbac).
- The ACL adapter is provided by [zend-expressive-authorization-acl](https://github.com/zendframework/zend-expressive-authorization-acl/).

Each adapter is installable via [Composer](https://getcomposer.org):

```bash
$ composer require zendframework/zend-expressive-authorization-rbac
# or
$ composer require zendframework/zend-expressive-authorization-acl
```

In each adapter, we use the **route name** as the resource. This means you
can specify if a role is authorized to access a specific HTTP _route_.
However, this is just one approach to implementing an authorization system; you
can create your own system by implementing the
[AuthorizationInterface](https://github.com/zendframework/zend-expressive-authorization/blob/master/src/AuthorizationInterface.php).

For more information on the adapters, please read the
[RBAC documentation](https://docs.zendframework.com/zend-expressive-authorization-rbac/)
and the [ACL documentation](https://docs.zendframework.com/zend-expressive-authorization-acl/).
