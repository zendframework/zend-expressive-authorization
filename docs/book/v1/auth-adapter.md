# Authorization adapters

You can choose an authorization adapter through the service container
configuration.

You need to specify the service for authentication using the name
`Zend\Expressive\Authorization\AuthorizationInterface`.

For instance, using [zend-servicemanager](https://github.com/zendframework/zend-servicemanager)
you can easily configure the authorization using aliases. Below is an example of
configuration using an ACL or RBAC adapter.


```
use Zend\Expressive\Authorization\AuthorizationInterface;
use Zend\Expressive\Authorization\Acl\ZendAcl;
use Zend\Expressive\Authorization\Rbac\ZendRbac;

return [
    // ...
    'dependencies' => [
        // ...
        'aliases' => [
            // ...
            AuthorizationInterface::class => ZendAcl::class,
            // or AuthorizationInterface::class => ZendRbac::class
        ]
    ]
];
```

The RBAC adapter is managed by [zend-expressive-authorization-rbac](https://github.com/zendframework/zend-expressive-authorization-rbac)
and ACL is managed by [zend-expressive-authorization-acl](https://github.com/zendframework/zend-expressive-authorization-acl/)
library.

If you want to use one of these adapters, you need to install via composer:

```bash
composer require zendframework/zend-expressive-authorization-rbac
# or
composer require zendframework/zend-expressive-authorization-acl
```

In both these adapters, we used the **route name** as resource. This means, you
can specify if a role is authorized to access a specific HTTP route or not.
This is just a general idea for implementing an authorization system. You can
create your own system implementing the [AuthorizationInterface](https://github.com/zendframework/zend-expressive-authorization/blob/master/src/AuthorizationInterface.php),
as reported above.

For more information about these adapters please read the [RBAC documentation](https://docs.zendframework.com/zend-expressive-authorization-rbac/)
and the [ACL documentation](https://docs.zendframework.com/zend-expressive-authorization-acl/).
