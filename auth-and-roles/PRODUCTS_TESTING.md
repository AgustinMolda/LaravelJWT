# 🛍️ TESTING ENDPOINTS DE PRODUCTOS

## 📋 ENDPOINTS DISPONIBLES

### 🔓 RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
- `POST /api/register` - Crear usuario
- `POST /api/login` - Iniciar sesión

### 🔐 RUTAS PROTEGIDAS (CON AUTENTICACIÓN)
- `GET /api/products` - Ver todos los productos (usuarios autenticados)
- `POST /api/logout` - Cerrar sesión
- `GET /api/me` - Obtener información del usuario

### 👑 RUTAS DE ADMINISTRADOR
- `POST /api/products` - Crear producto (solo admin)
- `GET /api/products/{id}` - Ver producto específico (solo admin)
- `PATCH /api/products/{id}` - Actualizar producto (solo admin)
- `DELETE /api/products/{id}` - Eliminar producto (solo admin)

---

## 🧪 PLAN DE TESTING

### PASO 1: CREAR USUARIOS DE PRUEBA

#### 1.1 Crear Usuario Normal
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuario Normal",
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

#### 1.2 Crear Usuario Administrador
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Administrador del Sistema",
    "email": "admin@test.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin"
  }'
```

**Respuesta esperada:**
```json
{
  "message": "User created successfully"
}
```

### PASO 2: OBTENER TOKENS JWT

#### 2.1 Login Usuario Normal
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

#### 2.2 Login Usuario Admin
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@test.com",
    "password": "password123"
  }'
```

**Respuesta esperada:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

---

## 🛍️ TESTING ENDPOINTS DE PRODUCTOS

### TEST 1: OBTENER PRODUCTOS (USUARIO NORMAL)

#### 1.1 Sin Productos (Lista Vacía)
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer USER_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "No products found"
}
```

### TEST 2: CREAR PRODUCTOS (SOLO ADMIN)

#### 2.1 Crear Producto Válido
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "name": "Laptop Gaming Pro",
    "price": 1299.99
  }'
```

**Respuesta esperada:**
```json
{
  "message": "product added successfully"
}
```

#### 2.2 Crear Producto con Nombre Muy Corto (ERROR)
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "name": "Laptop",
    "price": 1299.99
  }'
```

**Respuesta esperada:**
```json
{
  "error": {
    "name": ["The name field must be at least 10 characters."]
  }
}
```

#### 2.3 Crear Producto sin Precio (ERROR)
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "name": "Laptop Gaming Pro"
  }'
```

**Respuesta esperada:**
```json
{
  "error": {
    "price": ["The price field is required."]
  }
}
```

#### 2.4 Usuario Normal Intenta Crear Producto (ERROR)
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer USER_TOKEN_HERE" \
  -d '{
    "name": "Laptop Gaming Pro",
    "price": 1299.99
  }'
```

**Respuesta esperada:**
```json
{
  "message": "You are not an ADMIN"
}
```

### TEST 3: OBTENER PRODUCTOS (DESPUÉS DE CREAR)

#### 3.1 Lista de Productos
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer USER_TOKEN_HERE"
```

**Respuesta esperada:**
```json
[
  {
    "id": 1,
    "name": "Laptop Gaming Pro",
    "price": "1299.99",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
]
```

### TEST 4: OBTENER PRODUCTO POR ID (SOLO ADMIN)

#### 4.1 Producto Existente
```bash
curl -X GET http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "id": 1,
  "name": "Laptop Gaming Pro",
  "price": "1299.99",
  "created_at": "2024-01-01T00:00:00.000000Z",
  "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### 4.2 Producto No Existente
```bash
curl -X GET http://localhost:8000/api/products/999 \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "Product not found"
}
```

#### 4.3 Usuario Normal Intenta Obtener Producto por ID (ERROR)
```bash
curl -X GET http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer USER_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "You are not an ADMIN"
}
```

### TEST 5: ACTUALIZAR PRODUCTO (SOLO ADMIN)

#### 5.1 Actualizar Nombre
```bash
curl -X PATCH http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "name": "Laptop Gaming Pro Updated"
  }'
```

**Respuesta esperada:**
```json
{
  "message": "Product updated successfully"
}
```

#### 5.2 Actualizar Precio
```bash
curl -X PATCH http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "price": 1499.99
  }'
```

**Respuesta esperada:**
```json
{
  "message": "Product updated successfully"
}
```

#### 5.3 Actualizar Ambos Campos
```bash
curl -X PATCH http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "name": "Laptop Gaming Pro Final",
    "price": 1599.99
  }'
```

**Respuesta esperada:**
```json
{
  "message": "Product updated successfully"
}
```

#### 5.4 Actualizar con Nombre Muy Corto (ERROR)
```bash
curl -X PATCH http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE" \
  -d '{
    "name": "Short"
  }'
```

**Respuesta esperada:**
```json
{
  "error": {
    "name": ["The name field must be at least 10 characters."]
  }
}
```

### TEST 6: ELIMINAR PRODUCTO (SOLO ADMIN)

#### 6.1 Eliminar Producto Existente
```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "Product deleted successfully"
}
```

#### 6.2 Eliminar Producto No Existente
```bash
curl -X DELETE http://localhost:8000/api/products/999 \
  -H "Authorization: Bearer ADMIN_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "Product not found"
}
```

#### 6.3 Usuario Normal Intenta Eliminar Producto (ERROR)
```bash
curl -X DELETE http://localhost:8000/api/products/1 \
  -H "Authorization: Bearer USER_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "You are not an ADMIN"
}
```

---

## 🔍 VERIFICACIÓN FINAL

### Verificar que la Lista Esté Vacía
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer USER_TOKEN_HERE"
```

**Respuesta esperada:**
```json
{
  "message": "No products found"
}
```

---

## 📝 NOTAS IMPORTANTES

### Errores Corregidos en el Código:
1. ✅ `'mina:10'` → `'min:10'` (validación de nombre)
2. ✅ `'somtimes'` → `'sometimes'` (validación de precio)
3. ✅ `updatePorductById` → `updateProductById` (nombre del método)
4. ✅ `402` → `422` (códigos de estado HTTP)
5. ✅ `$product->update()` → `$product->save()` (guardar cambios)
6. ✅ Agregada respuesta faltante en `deleteProductById`
7. ✅ Corregidos errores tipográficos en mensajes

### Validaciones Implementadas:
- **Nombre**: mínimo 10 caracteres, máximo 100
- **Precio**: debe ser numérico
- **Autenticación**: token JWT requerido
- **Autorización**: rol admin para operaciones CRUD

### Códigos de Estado HTTP:
- **200**: Operación exitosa
- **201**: Recurso creado
- **401**: No autenticado
- **403**: No autorizado (no es admin)
- **404**: Recurso no encontrado
- **422**: Datos inválidos

---

## 🚀 COMANDOS RÁPIDOS PARA POSTMAN

### Variables de Entorno:
- `base_url`: `http://localhost:8000`
- `user_token`: Token del usuario normal
- `admin_token`: Token del administrador

### Headers Comunes:
- `Content-Type`: `application/json`
- `Authorization`: `Bearer {{admin_token}}` o `Bearer {{user_token}}`

### URLs de Prueba:
- `{{base_url}}/api/register`
- `{{base_url}}/api/login`
- `{{base_url}}/api/products`
- `{{base_url}}/api/products/1` 