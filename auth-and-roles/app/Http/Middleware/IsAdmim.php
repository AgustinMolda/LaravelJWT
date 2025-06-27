<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MIDDLEWARE DE AUTORIZACIÓN DE ADMINISTRADORES
 * 
 * Este middleware verifica que el usuario autenticado tenga el rol de 'admin'
 * antes de permitir el acceso a rutas que requieren privilegios de administrador.
 * Se ejecuta después del middleware de autenticación.
 */
class IsAdmim
{
    /**
     * MANEJA LA SOLICITUD ENTRANTE
     * 
     * Este método verifica dos cosas:
     * 1. Que el usuario esté autenticado (tiene token JWT válido)
     * 2. Que el usuario tenga el rol de 'admin'
     *
     * @param Request $request - La solicitud HTTP entrante
     * @param Closure $next - Función que continúa el flujo de la aplicación
     * @return Response - Respuesta HTTP
     */
    public function handle(Request $request, Closure $next): Response
    {
        // OBTIENE EL USUARIO AUTENTICADO
        // auth('api')->user() devuelve el usuario del token JWT actual
        $user = auth('api')->user();

        // VERIFICACIÓN DE ROL DE ADMINISTRADOR
        // Comprueba que el usuario existe Y tiene el rol 'admin'
        if($user && $user->role === 'admin'){
            // USUARIO ES ADMIN - ACCESO PERMITIDO
            // Pasa la solicitud al siguiente middleware o controlador
            return $next($request);    
        }else{
            // USUARIO NO ES ADMIN - ACCESO DENEGADO
            // Devuelve error 403 (Forbidden) - usuario autenticado pero sin permisos
            return response()->json(['message'=>'You are not an ADMIN'],403);
        }
    }
}
