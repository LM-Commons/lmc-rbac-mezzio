---
title: Strategies
sidebar_position: 3
---

## What are strategies?

A strategy is an object that listens to the `AbstractGuardMiddleware::EVENT_NAME` event.
It is used to perform an action access to a resource is unauthorized by LmcRbacMezzio.

By default, LmcRbacMezzio does not register any strategy for you. 
There must be registered it in a config file under the
`'strategies'` subkey of the `'lmc_rbac` key:

```php
return [
    // other configs...
    
    'lmc_rbac' => [
        'strategies' => [
            \Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategy::class,
        ],
    ],
];
```
## Built-in strategies

LmcRbacMezzio comes with two built-in strategies: 
- `\Lmc\Rbac\Mezzio\Strategy\RedirectStrategy` 
- `\Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategy`.

### RedirectStrategy

This strategy allows your application to redirect any unauthorized request to another route
by optionally appending the previous URL as a query parameter.

To register it, copy-paste this code into a configuration file:

```php
return [
    // other configs...
    
    'lmc_rbac' => [
        'strategies' => [
            \Lmc\Rbac\Mezzio\Strategy\RedirectStrategy::class
        ],
    ],
];
```

You can configure the strategy using the `redirect_strategy` subkey:

```php
return [
    'lmc_rbac' => [
        'redirect_strategy' => [
            'redirect_when_connected'        => true,
            'redirect_to_route_connected'    => 'home',
            'redirect_to_route_disconnected' => 'login',
            'append_previous_uri'            => true,
            'previous_uri_query_key'         => 'redirectTo'
        ],
    ]
];
```

If users try to access an unauthorized resource (eg.: http://www.example.com/delete), they will be
redirected to the "login" route if is not connected and to the "home" route otherwise with the previous URL appended:

> http://www.example.com/login?redirectTo=http://www.example.com/delete

You can prevent redirection when a user is connected (i.e. so that the user gets a 403 page)
by setting `redirect_when_connected` to `false`.

### UnauthorizedStrategy

This strategy allows your application to render a template on any unauthorized request.

To register it, copy-paste this code into your Module.php class:

```php
return [
    // other configs...
    
    'lmc_rbac' => [
        'strategies' => [
            \Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategy::class,
        ],
    ],
];
```

You can configure the strategy using the `unauthorized_strategy` subkey:

```php
return [
    'lmc_rbac' => [
        'unauthorized_strategy' => [
            'template' => 'error::custom-403'
        ],
    ]
];
```

:::tip
By default, LmcRbacMezzio uses a template named `error::403`.
:::

## Creating custom strategies

Creating a custom strategy is rather easy. Let's say we want to create a strategy that integrates with
the [Mezzio Problem Details](https://docs.mezzio.dev/mezzio-problem-details/) module:

```php
namespace MyApplication\Strategy;

use Laminas\Mvc\MvcEvent;
use Lmc\Rbac\Mezzio\Strategy\AbstractStrategy;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

class ApiProblemStrategy extends AbstractStrategy
{
    public function __construct(
        private ProblemDetailsResponseFactory $problemDetailsFactory
    ) {
    }

    public function onUnAuthorized(Event $event): null|ResponseInterface
    {
        if ($event->getParam('request') instanceof RequestInterface) {
            $request = $event->getParam('request');
            return $this->problemDetailsFactory->createResponse(
                $request, 403,
                'Access unauthorized', '', '', [],
            );
        }
        return null;
    }
}
```

Register your strategy:

```php
return [
    // other configs...

    'lmc_rbac' => [
        'strategies' => [
            MyApplication\Strategy\ApiProblemStrategy::class,
        ],
    ],
];
```
