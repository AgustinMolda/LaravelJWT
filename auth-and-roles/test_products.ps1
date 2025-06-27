# Script de Testing para Endpoints de Productos
# Ejecutar: .\test_products.ps1

Write-Host "========================================" -ForegroundColor Green
Write-Host "    TESTING PRODUCTS API ENDPOINTS" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Variables
$baseUrl = "http://localhost:8000"
$userToken = ""
$adminToken = ""

# Función para hacer requests
function Invoke-APIRequest {
    param(
        [string]$Method,
        [string]$Endpoint,
        [string]$Body = "",
        [string]$Token = ""
    )
    
    $headers = @{
        "Content-Type" = "application/json"
    }
    
    if ($Token) {
        $headers["Authorization"] = "Bearer $Token"
    }
    
    try {
        if ($Body) {
            $response = Invoke-RestMethod -Uri "$baseUrl$Endpoint" -Method $Method -Headers $headers -Body $Body
        } else {
            $response = Invoke-RestMethod -Uri "$baseUrl$Endpoint" -Method $Method -Headers $headers
        }
        return $response
    }
    catch {
        Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
        return $null
    }
}

# Función para mostrar resultados
function Show-Result {
    param(
        [string]$TestName,
        [object]$Result,
        [string]$Expected = ""
    )
    
    Write-Host "`n--- $TestName ---" -ForegroundColor Yellow
    if ($Result) {
        Write-Host "✅ ÉXITO" -ForegroundColor Green
        $Result | ConvertTo-Json -Depth 3
    } else {
        Write-Host "❌ FALLO" -ForegroundColor Red
        if ($Expected) {
            Write-Host "Esperado: $Expected" -ForegroundColor Gray
        }
    }
}

# PASO 1: Crear usuarios de prueba
Write-Host "PASO 1: Creando usuarios de prueba..." -ForegroundColor Cyan

# Crear usuario normal
$userData = @{
    name = "Usuario Normal"
    email = "usuario@test.com"
    password = "password123"
    password_confirmation = "password123"
    role = "user"
} | ConvertTo-Json

$result = Invoke-APIRequest -Method "POST" -Endpoint "/api/register" -Body $userData
Show-Result "Crear Usuario Normal" $result

# Crear usuario admin
$adminData = @{
    name = "Administrador del Sistema"
    email = "admin@test.com"
    password = "password123"
    password_confirmation = "password123"
    role = "admin"
} | ConvertTo-Json

$result = Invoke-APIRequest -Method "POST" -Endpoint "/api/register" -Body $adminData
Show-Result "Crear Usuario Admin" $result

# PASO 2: Obtener tokens
Write-Host "`nPASO 2: Obteniendo tokens JWT..." -ForegroundColor Cyan

# Login usuario normal
$loginUserData = @{
    email = "usuario@test.com"
    password = "password123"
} | ConvertTo-Json

$result = Invoke-APIRequest -Method "POST" -Endpoint "/api/login" -Body $loginUserData
if ($result) {
    $userToken = $result.token
    Show-Result "Login Usuario Normal" $result
}

# Login usuario admin
$loginAdminData = @{
    email = "admin@test.com"
    password = "password123"
} | ConvertTo-Json

$result = Invoke-APIRequest -Method "POST" -Endpoint "/api/login" -Body $loginAdminData
if ($result) {
    $adminToken = $result.token
    Show-Result "Login Usuario Admin" $result
}

# PASO 3: Testing endpoints de productos
Write-Host "`nPASO 3: Testing endpoints de productos..." -ForegroundColor Cyan

# Test 1: Obtener productos (lista vacía)
$result = Invoke-APIRequest -Method "GET" -Endpoint "/api/products" -Token $userToken
Show-Result "Obtener Productos (Lista Vacía)" $result

# Test 2: Crear producto (admin)
$productData = @{
    name = "Laptop Gaming Pro"
    price = 1299.99
} | ConvertTo-Json

$result = Invoke-APIRequest -Method "POST" -Endpoint "/api/products" -Body $productData -Token $adminToken
Show-Result "Crear Producto (Admin)" $result

# Test 3: Usuario normal intenta crear producto (debe fallar)
$result = Invoke-APIRequest -Method "POST" -Endpoint "/api/products" -Body $productData -Token $userToken
Show-Result "Usuario Normal Crear Producto (Debe Fallar)" $result

# Test 4: Obtener productos (con productos)
$result = Invoke-APIRequest -Method "GET" -Endpoint "/api/products" -Token $userToken
Show-Result "Obtener Productos (Con Productos)" $result

# Test 5: Obtener producto por ID (admin)
$result = Invoke-APIRequest -Method "GET" -Endpoint "/api/products/1" -Token $adminToken
Show-Result "Obtener Producto por ID (Admin)" $result

# Test 6: Usuario normal intenta obtener producto por ID (debe fallar)
$result = Invoke-APIRequest -Method "GET" -Endpoint "/api/products/1" -Token $userToken
Show-Result "Usuario Normal Obtener Producto por ID (Debe Fallar)" $result

# Test 7: Actualizar producto (admin)
$updateData = @{
    name = "Laptop Gaming Pro Updated"
    price = 1499.99
} | ConvertTo-Json

$result = Invoke-APIRequest -Method "PATCH" -Endpoint "/api/products/1" -Body $updateData -Token $adminToken
Show-Result "Actualizar Producto (Admin)" $result

# Test 8: Eliminar producto (admin)
$result = Invoke-APIRequest -Method "DELETE" -Endpoint "/api/products/1" -Token $adminToken
Show-Result "Eliminar Producto (Admin)" $result

# Test 9: Verificar lista vacía
$result = Invoke-APIRequest -Method "GET" -Endpoint "/api/products" -Token $userToken
Show-Result "Verificar Lista Vacía" $result

Write-Host "`n========================================" -ForegroundColor Green
Write-Host "    TESTING COMPLETADO" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Mostrar tokens para uso manual
Write-Host "Tokens obtenidos:" -ForegroundColor Yellow
Write-Host "User Token: $userToken" -ForegroundColor Gray
Write-Host "Admin Token: $adminToken" -ForegroundColor Gray
Write-Host ""

Write-Host "Puedes usar estos tokens en Postman para pruebas manuales." -ForegroundColor Cyan 