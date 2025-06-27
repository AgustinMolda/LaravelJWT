<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * MIDDLEWARE DE AUTENTICACIÓN DE USUARIOS
 * 
 * Este middleware verifica que el usuario esté autenticado antes de
 * permitir el acceso a rutas protegidas. Se ejecuta en cada solicitud
 * que requiera autenticación.
 */
class IsUserAuth
{
    /**
     * MANEJA LA SOLICITUD ENTRANTE
     * 
     * Este método se ejecuta antes de que la solicitud llegue al controlador.
     * Verifica si hay un usuario autenticado usando el guard 'api' (JWT).
     *
     * @param Request $request - La solicitud HTTP entrante
     * @param Closure $next - Función que continúa el flujo de la aplicación
     * @return Response - Respuesta HTTP
     */
    public function handle(Request $request, Closure $next): Response
    {
        // VERIFICACIÓN DE AUTENTICACIÓN
        // auth('api')->user() verifica si hay un token JWT válido
        // y devuelve el usuario asociado a ese token
        if(auth('api')->user()){
            // USUARIO AUTENTICADO - CONTINÚA CON LA SOLICITUD
            // $next($request) pasa la solicitud al siguiente middleware o controlador
            return $next($request);
        }else{
            // USUARIO NO AUTENTICADO - ACCESO DENEGADO
            // Devuelve error 401 (Unauthorized) con mensaje claro
            return response()->json(['message'=>'Unauthorized'],401);
        }
    }
}
