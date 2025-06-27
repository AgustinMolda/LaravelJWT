<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller{

    /**
     * REGISTRO DE USUARIOS
     * 
     * Este método maneja la creación de nuevos usuarios en el sistema.
     * Incluye validación de datos, encriptación de contraseñas y
     * verificación de unicidad de email.
     */
    public function register(Request $request){
        
        // VALIDACIÓN DE DATOS DE ENTRADA
        // Esta validación asegura que los datos cumplan con los requisitos de seguridad
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|min:10|max:100',        // Nombre obligatorio, entre 10-100 caracteres
            'role' => 'required|string|in:admin,user',         // Rol debe ser admin o user (evita roles maliciosos)
            'email' => 'required|string|email|min:10|max:50|unique:users', // Email único y válido
            'password'=> 'required|string|min:10|confirmed'    // Contraseña con confirmación (doble verificación)
        ]);

        // SI LA VALIDACIÓN FALLA, DEVUELVE ERRORES
        if($validator->fails()){
            return response()->json(['error'=> $validator->errors()],422); // 422 = Unprocessable Entity
        }

        // CREACIÓN SEGURA DEL USUARIO
        // Los datos se insertan usando el modelo User que tiene fillable definido
        User::create([
            'name'=>$request->get('name'),                     // get() es más seguro que direct access
            'role'=>$request->get('role'),
            'email'=>$request->get('email'),
            'password'=>bcrypt($request->get('password')),     // ENCRIPTACIÓN: Hash de la contraseña
        ]);

        // RESPUESTA EXITOSA
        return response()->json(['message'=>'User created successfully'],201); // 201 = Created
    }

    /**
     * AUTENTICACIÓN DE USUARIOS (LOGIN)
     * 
     * Este método verifica las credenciales del usuario y genera un token JWT
     * para mantener la sesión activa de forma segura.
     */
    public function login(Request $request){
        
        // VALIDACIÓN DE CREDENCIALES
        $validator = Validator::make($request->all(),[
            'email'=>'required|string|email|min:10|max:50',    // Email válido
            'password'=> 'required|string|min:10',             // Contraseña mínima 10 caracteres
        ]);

        // SI LA VALIDACIÓN FALLA
        if($validator->fails()){
            return response()->json(["error"=>$validator->errors()],422);
        }

        // EXTRACCIÓN SEGURA DE CREDENCIALES
        $credentials= $request->only(['email','password']);    // Solo extrae email y password
        
        try {
            // INTENTO DE AUTENTICACIÓN JWT
            // JWTAuth::attempt() compara las credenciales con la base de datos
            // y verifica automáticamente el hash de la contraseña
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401); // 401 = Unauthorized
            }
            
            // AUTENTICACIÓN EXITOSA - DEVUELVE TOKEN JWT
            // Este token contiene información del usuario y expiración
            return response()->json(['token' => $token], 200);
            
        } catch (JWTException $e) {
            // MANEJO DE ERRORES JWT
            return response()->json(['error' => 'Could not create token', $e], 500);
        }
    }

    /**
     * OBTENER INFORMACIÓN DEL USUARIO AUTENTICADO
     * 
     * Este método devuelve la información del usuario actualmente autenticado
     * usando el token JWT proporcionado en el header Authorization.
     */
    public function getUser(){
        // Auth::user() obtiene el usuario del token JWT actual
        $user = Auth::user();
        return response()->json($user,200);
    }

    /**
     * CERRAR SESIÓN (LOGOUT)
     * 
     * Este método invalida el token JWT actual, lo que significa que
     * el usuario ya no podrá usar ese token para acceder a rutas protegidas.
     */
    public function logout(){
        // INVALIDACIÓN DEL TOKEN JWT
        // Esto hace que el token actual ya no sea válido
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message'=>'log out successfully'],200);
    }
}
