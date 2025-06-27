# 🔐 SEGURIDAD Y AUTENTICACIÓN EXPLICADA

## 📋 ÍNDICE
1. [¿Qué es JWT?](#qué-es-jwt)
2. [Flujo de Autenticación](#flujo-de-autenticación)
3. [Validaciones de Seguridad](#validaciones-de-seguridad)
4. [Middlewares de Seguridad](#middlewares-de-seguridad)
5. [Protección de Datos](#protección-de-datos)
6. [Códigos de Estado HTTP](#códigos-de-estado-http)
7. [Mejores Prácticas Implementadas](#mejores-prácticas-implementadas)

---

## 🔑 ¿QUÉ ES JWT?

**JWT (JSON Web Token)** es un estándar para crear tokens de acceso que permiten la comunicación segura entre aplicaciones.

### Estructura de un JWT:
```
eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c
```

**Partes del token:**
- **Header**: Algoritmo de encriptación
- **Payload**: Datos del usuario (ID, rol, expiración)
- **Signature**: Firma digital para verificar autenticidad

### Ventajas de JWT:
- ✅ **Stateless**: No necesita almacenar sesiones en el servidor
- ✅ **Escalable**: Funciona con múltiples servidores
- ✅ **Seguro**: Firma digital previene manipulación
- ✅ **Portable**: Se puede usar en diferentes dominios

---

## 🔄 FLUJO DE AUTENTICACIÓN

### 1. REGISTRO DE USUARIO
```
Cliente → POST /api/register → Validación → Encriptación → Base de Datos
```

**Pasos:**
1. Cliente envía datos (nombre, email, contraseña, rol)
2. **Validación**: Verifica formato, longitud, unicidad
3. **Encriptación**: `bcrypt()` hashea la contraseña
4. **Almacenamiento**: Usuario se guarda en BD
5. **Respuesta**: Confirmación de creación exitosa

### 2. LOGIN DE USUARIO
```
Cliente → POST /api/login → Verificación → Generación JWT → Respuesta
```

**Pasos:**
1. Cliente envía credenciales (email, contraseña)
2. **Verificación**: Compara con datos en BD
3. **Generación JWT**: Crea token con datos del usuario
4. **Respuesta**: Devuelve token al cliente

### 3. ACCESO A RUTAS PROTEGIDAS
```
Cliente → Header: Authorization: Bearer TOKEN → Middleware → Controlador
```

**Pasos:**
1. Cliente incluye token en header
2. **Middleware**: Verifica validez del token
3. **Autorización**: Verifica rol si es necesario
4. **Acceso**: Permite acceso al recurso

---

## 🛡️ VALIDACIONES DE SEGURIDAD

### Validaciones en Registro:
```php
'name' => 'required|string|min:10|max:100'        // Previne nombres muy cortos/largos
'role' => 'required|string|in:admin,user'         // Solo roles permitidos
'email' => 'required|string|email|min:10|max:50|unique:users' // Email único
'password'=> 'required|string|min:10|confirmed'   // Contraseña fuerte + confirmación
```

### Validaciones en Login:
```php
'email'=>'required|string|email|min:10|max:50'    // Email válido
'password'=> 'required|string|min:10'             // Contraseña mínima
```

### ¿Por qué estas validaciones?
- **Longitud mínima**: Previene ataques de fuerza bruta
- **Roles específicos**: Evita roles maliciosos
- **Email único**: Previene duplicados
- **Confirmación**: Evita errores de escritura

---

## 🔒 MIDDLEWARES DE SEGURIDAD

### 1. IsUserAuth (Autenticación)
```php
// Verifica que el usuario tenga token JWT válido
if(auth('api')->user()){
    return $next($request); // ✅ Acceso permitido
}else{
    return response()->json(['message'=>'Unauthorized'],401); // ❌ Acceso denegado
}
```

**¿Qué hace?**
- Extrae token del header `Authorization: Bearer TOKEN`
- Verifica que el token sea válido y no haya expirado
- Obtiene el usuario asociado al token
- Permite o deniega el acceso

### 2. IsAdmim (Autorización)
```php
// Verifica que el usuario sea administrador
if($user && $user->role === 'admin'){
    return $next($request); // ✅ Admin puede acceder
}else{
    return response()->json(['message'=>'You are not an ADMIN'],403); // ❌ No es admin
}
```

**¿Qué hace?**
- Se ejecuta DESPUÉS de IsUserAuth
- Verifica que el usuario autenticado tenga rol 'admin'
- Permite acceso solo a administradores

---

## 🛡️ PROTECCIÓN DE DATOS

### 1. Campos Fillable (Asignación Masiva)
```php
protected $fillable = [
    'name', 'role', 'email', 'password'
];
```
**¿Por qué?**
- Solo estos campos se pueden asignar masivamente
- Previene asignación de campos no deseados
- Protege contra ataques de inyección de datos

### 2. Campos Hidden (Ocultos)
```php
protected $hidden = [
    'password', 'remember_token'
];
```
**¿Por qué?**
- Estos campos NO se incluyen en respuestas JSON
- Previene exposición de datos sensibles
- La contraseña nunca se devuelve al cliente

### 3. Encriptación Automática
```php
'password' => 'hashed' // Se hashea automáticamente
```
**¿Por qué?**
- Las contraseñas se hashean antes de guardar
- Usa bcrypt (algoritmo seguro)
- Previene acceso a contraseñas en texto plano

---

## 📊 CÓDIGOS DE ESTADO HTTP

### Códigos de Éxito:
- **200 OK**: Operación exitosa
- **201 Created**: Recurso creado exitosamente

### Códigos de Error de Cliente:
- **401 Unauthorized**: No autenticado (sin token o token inválido)
- **403 Forbidden**: Autenticado pero sin permisos (no es admin)
- **422 Unprocessable Entity**: Datos inválidos (errores de validación)

### Códigos de Error de Servidor:
- **500 Internal Server Error**: Error interno del servidor

---

## ✅ MEJORES PRÁCTICAS IMPLEMENTADAS

### 1. Validación de Entrada
- ✅ Validación en el servidor (nunca confiar en el cliente)
- ✅ Reglas específicas para cada campo
- ✅ Mensajes de error claros

### 2. Encriptación de Contraseñas
- ✅ Uso de bcrypt (algoritmo seguro)
- ✅ Hash automático en el modelo
- ✅ Nunca almacenar contraseñas en texto plano

### 3. Tokens JWT
- ✅ Tokens con expiración
- ✅ Firma digital para verificar autenticidad
- ✅ Invalidación en logout

### 4. Control de Acceso
- ✅ Autenticación (¿quién eres?)
- ✅ Autorización (¿qué puedes hacer?)
- ✅ Middlewares separados para cada nivel

### 5. Respuestas Seguras
- ✅ No exponer información sensible
- ✅ Códigos de estado HTTP apropiados
- ✅ Mensajes de error genéricos (no revelar detalles internos)

---

## 🚀 CÓMO USAR LA API DE FORMA SEGURA

### 1. Registro:
```bash
POST /api/register
{
  "name": "Usuario de Prueba",
  "email": "usuario@test.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user"
}
```

### 2. Login:
```bash
POST /api/login
{
  "email": "usuario@test.com",
  "password": "password123"
}
```

### 3. Acceso a Rutas Protegidas:
```bash
GET /api/me
Headers: Authorization: Bearer YOUR_JWT_TOKEN
```

### 4. Logout:
```bash
POST /api/logout
Headers: Authorization: Bearer YOUR_JWT_TOKEN
```

---

## 🔍 MONITOREO Y LOGS

### ¿Qué monitorear?
- Intentos de login fallidos
- Accesos a rutas de administrador
- Tokens expirados
- Errores de validación

### Recomendaciones:
- Implementar rate limiting
- Logs de auditoría
- Alertas de seguridad
- Monitoreo de tokens expirados

---

## 📚 RECURSOS ADICIONALES

- [Documentación JWT](https://jwt.io/)
- [Laravel JWT Auth](https://github.com/tymondesigns/jwt-auth)
- [OWASP Security Guidelines](https://owasp.org/)
- [Laravel Security Best Practices](https://laravel.com/docs/security) 