<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\IsAdmim;
use App\Http\Middleware\IsUserAuth;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * RUTAS DE LA API
 * 
 * Este archivo define todas las rutas de la API REST.
 * Las rutas están organizadas por nivel de seguridad:
 * - Rutas públicas: No requieren autenticación
 * - Rutas protegidas: Requieren autenticación JWT
 * - Rutas de administrador: Requieren rol de admin
 */

// ========================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// ========================================
// Estas rutas pueden ser accedidas sin token JWT

Route::post('/register',[AuthController::class,'register']);  // Crear nuevo usuario
Route::post('/login',[AuthController::class,'login']);        // Iniciar sesión

// ========================================
// RUTAS PROTEGIDAS (CON AUTENTICACIÓN)
// ========================================
// Estas rutas requieren un token JWT válido en el header Authorization

Route::middleware([IsUserAuth::class])->group(function(){
    
    // RUTAS DE AUTENTICACIÓN PARA USUARIOS LOGUEADOS
    Route::controller(AuthController::class)->group(function(){
        Route::post('logout','logout');    // Cerrar sesión (invalida token)
        Route::get('me', 'getUser');       // Obtener información del usuario actual
    });

    // RUTAS DE PRODUCTOS PARA USUARIOS AUTENTICADOS
    Route::get('products',[ProductController::class,'getProducts']); // Ver productos
});

// ========================================
// RUTAS DE ADMINISTRADOR
// ========================================
// Estas rutas requieren autenticación Y rol de administrador

Route::middleware([IsAdmim::class])->group(function(){
    
    // RUTAS CRUD DE PRODUCTOS (SOLO ADMIN)
    Route::controller(ProductController::class)->group(function(){
        Route::post('products','addProduct');           // Crear producto
        Route::get('/products/{id}','getProductById');  // Ver producto específico
        Route::patch('/products/{id}','updateProductById'); // Actualizar producto
        Route::delete('/products/{id}','deleteProductById'); // Eliminar producto
    });
});

/**
 * EXPLICACIÓN DE LA SEGURIDAD EN RUTAS:
 * 
 * 1. MIDDLEWARE IsUserAuth:
 *    - Verifica que el usuario tenga un token JWT válido
 *    - Si no tiene token, devuelve 401 Unauthorized
 *    - Se aplica a todas las rutas dentro del grupo
 * 
 * 2. MIDDLEWARE IsAdmim:
 *    - Verifica que el usuario esté autenticado Y tenga rol 'admin'
 *    - Si no es admin, devuelve 403 Forbidden
 *    - Se ejecuta después de IsUserAuth
 * 
 * 3. FLUJO DE SEGURIDAD:
 *    - Usuario hace login → recibe token JWT
 *    - Usuario incluye token en header: Authorization: Bearer TOKEN
 *    - Middleware verifica token y rol
 *    - Si todo está bien, accede a la ruta
 *    - Si no, recibe error de autenticación/autorización
 */
