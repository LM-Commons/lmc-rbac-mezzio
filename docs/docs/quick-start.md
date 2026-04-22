---
title: Quick Start
sidebar_position: 1
---

LM-Commons RBAC Mezzio provides guards and strategies to implement Role Based
Access Controls in Mezzio applications.

LmcRbacMezzio provides additional features on top of [LmcRbac](https://lm-commons.github.io/LmcRbac)
that are suitable for a Laminas Mezzio application:

- Guards and middlewares that acts like a firewall allowing access to routes to authorized users.
- Strategies to execute when unauthorized access occurs such as redirection and error responses

It is highly recommended to first go through the concepts and usage of LmcRbac before using
the functionalities of LmcRbacMezzio.

## Requirements

- PHP 8.3 or higher
- LmcRbac v2 (installed by default)

## Installation

LmcRbacMezzio only officially supports installation through Composer.

Install the module:

```sh
$ composer require lm-commons/lmc-rbac-mezzio
```

You will be prompted by the Laminas Component Installer plugin to inject the
component into your `config/config.php` file.

## Quick Start

Before you start configuring LmcRbacMezzio, you must set up [LmcRbac](https://lm-commons.github.io/LmcRbac)
first. Please follow the [instructions](https://lm-commons.github.io/LmcRbac/docs/gettingstarted) in LmcRbac documentation.

## Adding middlewares

LmcRbacMezzio provides two middlewares: `UnauthorizedHandler` and `GuardMiddleware`.

The `UnauthorizedHandler` is a middleware that handles unauthorized exception. It is responsible for
performing the necessary logic to determine the strategy to use when an unauthorized excpetion occurs.
It should be added to the pipeline after the `RoutingMiddleware` and before the `GuardMiddleware`.

The `GuardMiddleware` is a middleware that blocks access to routes based on the guard configuration.
If access is denied, and `UnauthorizedException` exeption is thrown and is handled by  the `UnauthorizedHandler` middleware.
It should be added to the pipeline after the `UnauthorizedHandler` and after an authentication
middleware (if any) but before the `DispatchMiddleware`.

In a typical Mezzio application:, the `UnauthorizedHandler` and `GuardMiddleware` middlewares are 
added to the pipeline in the `config/pipeline.php` file:


## Adding a guard

A guard allows your application to block access to routes using a simple syntax. For instance, this configuration
grants access to any route that begins with `admin` (or is exactly `admin`) to the `admin` role only:

```php
return [
    'lmc_rbac' => [
        'guards' => [
	        'Lmc\Rbac\Mezzio\Guard\RouteGuard' => [
                'admin*' => ['admin']
	        ]
        ]
    ]
];
```

```php
<?php

declare(strict_types=1);

use Laminas\Stratigility\Middleware\ErrorHandler;
use Lmc\Rbac\Mezzio\Middleware\GuardMiddleware;
use Mezzio\Application;
use Mezzio\Authentication\AuthenticationMiddleware;
use Mezzio\Handler\NotFoundHandler;
use Mezzio\Helper\ServerUrlMiddleware;
use Mezzio\Helper\UrlHelperMiddleware;
use Mezzio\MiddlewareFactory;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;
use Mezzio\Session\SessionMiddleware;
use Psr\Container\ContainerInterface;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container): void {
    $app->pipe(ErrorHandler::class);
    $app->pipe(ServerUrlMiddleware::class);

    $app->pipe(SessionMiddleware::class);

    $app->pipe(RouteMiddleware::class);
    $app->pipe(UrlHelperMiddleware::class);
    $app->pipe(AuthenticationMiddleware::class);
    $app->pipe(GuardMiddleware::class);  // <---- here

    $app->pipe(DispatchMiddleware::class);

    $app->pipe(NotFoundHandler::class);
};
```


LmcRbacMezzio has several built-in guards, and you can also register your own guards. For more information, refer
[to this section](guards.md#built-in-guards).

## Registering a strategy

When a guard blocks access to a route, LmcRbacMezzio automatically performs some logic for you depending on the view strategy used.

For instance, if you want LmcRbacMezzio to automatically redirect all unauthorized requests to the "login" route,
add the following code in a configuration file:

```php
return [
    'lmc_rbac' => [
        'strategies'        => [
            Lmc\Rbac\Mezzio\Strategy\RedirectStrategy::class => [
                'redirect_when_connected'        => false,
                'redirect_to_route_disconnected' => 'login',
                'previous_uri_query_key'         => 'redirect',
            ],
        ],
    ],
];

```

By default, `RedirectStrategy` redirects all unauthorized requests to a route named "login" when the user is not connected
and to a route named "home" when the user is connected. This is entirely configurable.

:::warning
For flexibility purposes, LmcRbacMezzio **does not** register any strategy for you by default!
:::

For more information about built-in strategies, refer [to this section](strategies.md#built-in-strategies)
in the [Strategies](strategies.md) section.
