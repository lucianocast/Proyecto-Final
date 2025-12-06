@echo off
echo ================================
echo   PREPARACION DATOS DE PRUEBA
echo   Sistema de Pasteleria
echo ================================
echo.

echo [1/5] Limpiando base de datos anterior...
php artisan migrate:fresh --force
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Fallo al ejecutar migraciones
    pause
    exit /b 1
)
echo      OK - Base de datos limpia
echo.

echo [2/5] Creando datos de prueba...
php artisan db:seed --class=DatosPruebaEntregaSeeder
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Fallo al crear datos de prueba
    pause
    exit /b 1
)
echo      OK - Datos de prueba creados
echo.

echo [3/5] Ejecutando tests para verificar sistema...
php artisan test --testsuite=Feature
if %ERRORLEVEL% NEQ 0 (
    echo ADVERTENCIA: Algunos tests fallaron
)
echo      OK - Tests ejecutados
echo.

echo [4/5] Verificando estado de datos...
php artisan tinker --execute="echo 'Clientes: ' . \App\Models\Cliente::count() . PHP_EOL; echo 'Productos: ' . \App\Models\Producto::count() . PHP_EOL; echo 'Insumos: ' . \App\Models\Insumo::count() . PHP_EOL; echo 'Pedidos: ' . \App\Models\Pedido::count() . PHP_EOL; echo 'Proveedores: ' . \App\Models\Proveedor::count() . PHP_EOL;"
echo.

echo [5/5] Generando resumen de accesos...
echo ================================
echo   CREDENCIALES DE ACCESO
echo ================================
echo.
echo ADMIN:
echo   URL: http://127.0.0.1:8000/admin
echo   Email: admin@test.com
echo   Pass: password
echo.
echo VENDEDOR:
echo   URL: http://127.0.0.1:8000
echo   Email: vendedor@test.com
echo   Pass: password
echo.
echo CLIENTE WEB:
echo   URL: http://127.0.0.1:8000
echo   Email: cliente@test.com
echo   Pass: password
echo.
echo MAILPIT (emails):
echo   URL: http://localhost:8025
echo.
echo ================================
echo.
echo Listo! Sistema preparado para demostracion.
echo Presiona cualquier tecla para iniciar servidor...
pause > nul

echo.
echo Iniciando servidor Laravel...
start cmd /k "php artisan serve"

echo.
echo Iniciando Mailpit (si esta instalado)...
start cmd /k "mailpit"

echo.
echo Listo! Abre tu navegador en http://127.0.0.1:8000
echo.
pause
