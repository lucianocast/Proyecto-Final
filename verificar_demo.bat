@echo off
echo ================================
echo   VERIFICACION PRE-GRABACION
echo ================================
echo.

echo Verificando estado del sistema...
echo.

php artisan tinker --execute="echo '=== DATOS CARGADOS ===' . PHP_EOL; echo 'Clientes: ' . \App\Models\Cliente::count() . ' (esperado: 8)' . PHP_EOL; echo 'Productos: ' . \App\Models\Producto::count() . ' (esperado: 6)' . PHP_EOL; echo 'Insumos: ' . \App\Models\Insumo::count() . ' (esperado: 10)' . PHP_EOL; echo 'Pedidos Confirmados: ' . \App\Models\Pedido::where('status', 'confirmado')->count() . ' (esperado: 3)' . PHP_EOL; echo 'Proveedores: ' . \App\Models\Proveedor::count() . ' (esperado: 3)' . PHP_EOL; echo 'Recetas: ' . \App\Models\Receta::count() . ' (esperado: 6)' . PHP_EOL; echo 'Lotes con Stock: ' . \App\Models\Lote::where('cantidad_actual', '>', 0)->count() . PHP_EOL; echo PHP_EOL; echo '=== ACCESOS ===' . PHP_EOL; echo 'Admin existe: ' . (\App\Models\User::where('email', 'admin@test.com')->exists() ? 'SI' : 'NO') . PHP_EOL; echo 'Vendedor existe: ' . (\App\Models\User::where('email', 'vendedor@test.com')->exists() ? 'SI' : 'NO') . PHP_EOL;"

echo.
echo ================================
echo   CHECKLIST FINAL
echo ================================
echo.
echo [ ] Servidor Laravel corriendo?    (php artisan serve)
echo [ ] Mailpit corriendo?             (localhost:8025)
echo [ ] Navegador con pestanas listas?
echo     - Admin:    http://127.0.0.1:8000/admin
echo     - Catalogo: http://127.0.0.1:8000
echo     - Mailpit:  http://localhost:8025
echo [ ] OBS Studio abierto y configurado?
echo [ ] Microfono funcionando?
echo [ ] Guion de demostracion a mano?
echo.
echo ================================
echo.
echo Si todo esta OK, presiona Enter para continuar...
pause > nul

echo.
echo Abriendo URLs en el navegador...
start http://127.0.0.1:8000/admin
timeout /t 2 > nul
start http://127.0.0.1:8000
timeout /t 2 > nul
start http://localhost:8025
timeout /t 2 > nul

echo.
echo Listo para GRABAR!
echo.
echo Recuerda:
echo  - NO perder tiempo explicando contexto
echo  - Mostrar FLUJOS completos
echo  - Demostrar VALIDACIONES
echo  - Duracion MAX: 30 minutos
echo.
pause
