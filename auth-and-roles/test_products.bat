@echo off
echo ========================================
echo    TESTING PRODUCTS API ENDPOINTS
echo ========================================
echo.

echo 1. Starting Laravel development server...
echo    Server will be available at: http://localhost:8000
echo    Press Ctrl+C to stop the server
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause 