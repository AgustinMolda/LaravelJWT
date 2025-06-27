# Solución para Error SSL/TLS en Postman

## Error: "Client network socket disconnected before secure TLS connection was established"

Este error ocurre cuando Postman no puede establecer una conexión segura con el servidor. Aquí tienes varias soluciones:

## Solución 1: Deshabilitar Verificación SSL en Postman

### Para una solicitud específica:
1. Abre tu solicitud en Postman
2. Ve a la pestaña **"Settings"** (Configuración)
3. Desactiva la opción **"SSL certificate verification"**
4. Guarda la configuración

### Para toda la colección:
1. Haz clic derecho en tu colección
2. Selecciona **"Edit"**
3. Ve a la pestaña **"Settings"**
4. Desactiva **"SSL certificate verification"**

## Solución 2: Configurar el Certificado SSL

### Opción A: Usar certificado autofirmado
1. En Postman, ve a **Settings** → **General**
2. Desactiva **"SSL certificate verification"**
3. O configura un certificado personalizado en **Settings** → **Certificates**

### Opción B: Importar certificado del servidor
1. Descarga el certificado SSL de tu servidor
2. En Postman: **Settings** → **Certificates**
3. Agrega el certificado con el host correspondiente

## Solución 3: Configurar Laravel para Desarrollo Local

### Opción A: Usar HTTP en lugar de HTTPS para desarrollo

Si estás en desarrollo local, puedes usar HTTP en lugar de HTTPS:

```bash
# En lugar de https://localhost:8000
# Usa http://localhost:8000
```

### Opción B: Configurar HTTPS local con certificado válido

1. **Instalar mkcert:**
```bash
# Windows (con chocolatey)
choco install mkcert

# O descargar desde: https://github.com/FiloSottile/mkcert/releases
```

2. **Generar certificado local:**
```bash
mkcert -install
mkcert localhost 127.0.0.1 ::1
```

3. **Configurar Laravel para usar HTTPS:**
```bash
# En tu archivo .env
APP_URL=https://localhost:8000
```

4. **Ejecutar Laravel con HTTPS:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Solución 4: Configurar Postman para Ignorar Errores SSL

### Configuración global en Postman:
1. Ve a **Settings** (⚙️) en la esquina superior derecha
2. Selecciona **"General"**
3. Desactiva **"SSL certificate verification"**
4. Reinicia Postman

### Configuración por solicitud:
1. En tu solicitud, ve a la pestaña **"Settings"**
2. Desactiva **"SSL certificate verification"**
3. En **"Proxy"**, asegúrate de que esté desactivado si no lo necesitas

## Solución 5: Verificar Configuración de Red

### Verificar firewall:
1. Asegúrate de que el puerto 8000 esté abierto
2. Verifica que tu firewall no esté bloqueando la conexión

### Verificar proxy:
1. En Postman: **Settings** → **Proxy**
2. Desactiva el proxy si no lo necesitas
3. O configura correctamente tu proxy corporativo

## Solución 6: Usar Variables de Entorno en Postman

Crea una variable de entorno para la URL base:

1. **Crear variable:**
   - Nombre: `base_url`
   - Valor: `http://localhost:8000` (sin HTTPS para desarrollo)

2. **Usar en solicitudes:**
   - URL: `{{base_url}}/api/register`

## Solución 7: Configurar Laravel para CORS

Si el problema persiste, verifica la configuración CORS:

```php
// En config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## Recomendación para Desarrollo

Para desarrollo local, la **Solución 1** (deshabilitar SSL verification) es la más rápida y práctica. Para producción, siempre usa certificados SSL válidos.

## URLs de Prueba Recomendadas

```
# Para desarrollo local (HTTP)
http://localhost:8000/api/register
http://localhost:8000/api/login

# Para producción (HTTPS)
https://tu-dominio.com/api/register
https://tu-dominio.com/api/login
``` 