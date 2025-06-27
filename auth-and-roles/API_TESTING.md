# API Testing Guide - Errores Corregidos

## Errores Encontrados y Corregidos

### 1. AuthController.php

**Error en validación de login (línea 40):**
- **Problema:** `'password'=> 'required|string|min10',` (faltaba el `:`)
- **Solución:** `'password'=> 'required|string|min:10',`

**Error en códigos de estado HTTP:**
- **Problema:** Se usaba código 402 para errores de validación
- **Solución:** Cambiado a 422 (Unprocessable Entity) que es el estándar correcto

**Error en nombre del método logout:**
- **Problema:** El método se llamaba `logOut` pero en las rutas se llamaba `logout`
- **Solución:** Cambiado a `logout` (sin mayúscula)

### 2. IsUserAuth.php

**Error en el middleware:**
- **Problema:** `return next($request);` (faltaba el `$`)
- **Solución:** `return $next($request);`

## Cómo Probar la API

### 1. Crear un nuevo usuario (POST /api/register)

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuario de Prueba",
    "email": "usuario@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
  }'
```

**Respuesta esperada:**
```json
{
  "message": "User created successfully"
}
```

### 2. Iniciar sesión (POST /api/login)

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "usuario@test.com",
    "password": "password123"
  }'
```

**Respuesta esperada:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

### 3. Obtener información del usuario (GET /api/me)

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. Cerrar sesión (POST /api/logout)

```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Notas Importantes

1. **Validaciones del registro:**
   - `name`: mínimo 10 caracteres, máximo 100
   - `email`: mínimo 10 caracteres, máximo 50, debe ser único
   - `password`: mínimo 10 caracteres, debe incluir confirmación
   - `role`: debe ser 'admin' o 'user'

2. **Validaciones del login:**
   - `email`: mínimo 10 caracteres, máximo 50
   - `password`: mínimo 10 caracteres

3. **Autenticación:**
   - Todas las rutas protegidas requieren el token JWT en el header `Authorization: Bearer TOKEN`
   - El guard por defecto está configurado como 'api' con driver 'jwt'

4. **Roles:**
   - Las rutas de productos requieren autenticación de usuario
   - Las operaciones CRUD de productos requieren rol de administrador 