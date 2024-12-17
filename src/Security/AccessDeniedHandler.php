<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    public function handle(Request $request, \Throwable $accessDeniedException): ?RedirectResponse
    {
        // Redirection vers la route "app_inventory"
        return new RedirectResponse('/unauthorized'); // Redirection vers la route "app_unauthorized"
    }
}
