<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // La columna users.rol fue eliminada al migrar a Spatie Permission;
        // la autorización por rol ahora vive en la tabla model_has_roles.
        abort_unless(
            $request->user()?->hasAnyRole($roles) ?? false,
            403,
            'No tienes permiso para acceder a esta sección.'
        );

        return $next($request);
    }
}
