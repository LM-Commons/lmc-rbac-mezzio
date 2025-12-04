---
sidebar_position: 4
title: Handlers
---

Although guards can be used to protect routes, it may be necessary for route handlers
to perform additional authorization logic.

Or, it may be possible to not use guards at all, and instead perform authorization logic in each route handler.

For example, the route `post/edit` may protected by a guard that checks that the user has the role and permission
that allow him to edit posts. But the route handler may also need to check that the user is the owner of the post
to be allowed to edit that specific post.

When a handler determines that the user is not allowed to perform the requested action, it should throw the
`Lmc\Rbac\Mezzio\AuthorizedException` exception. This exception will be caught by the
`Lmc\Rbac\Mezzio\UnauthorizedHandler` middleware and the configured strategies will be used to create the response.

Here is an example of such a handler using LmcRbac's AuthorizationService:

```php
<?php

use Lmc\Rbac\AuthorizationAwareTrait;
use Lmc\Rbac\Mezzio\Exception\AuthorizedException;
use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class EditPostHandler implements RequestHandlerInterface
{
    use AuthorizationAwareTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $postId = $request->getAttribute('postid');
        $post = $this->postRepository->find($postId);
        $user = $request->getAttribute(UserInterface::class);
        
        // the assertion could be set up in config instead 
        $this->authorizationService->setAssertion('edit.post', function ($permission, $identity, $post) {
            return $post->getOwnerId() === $identity->getId();
            }
        );   
       
        if (!$this->authorizationService->isGranted($user, 'edit.post', $post)) {
            throw new AuthorizedException('User not allowed to edit this post.');
        }
        // ...
    }
}
```
