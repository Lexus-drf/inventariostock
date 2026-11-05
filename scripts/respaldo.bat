@echo off
REM ====================================================================
REM  InventarioStock - Respaldo diario de la base de datos y las fotos
REM  Programarlo en el "Programador de tareas" de Windows (una vez al día).
REM  Cambie DESTINO por la letra de la memoria USB o del disco externo.
REM ====================================================================
set XAMPP=C:\xampp
set DESTINO=E:\respaldos_inventariostock
set PROYECTO=%XAMPP%\htdocs\inventariostock

for /f %%i in ('powershell -NoProfile -Command "Get-Date -Format yyyy-MM-dd_HHmm"') do set FECHA=%%i

if not exist "%DESTINO%" mkdir "%DESTINO%"

echo Respaldando la base de datos...
"%XAMPP%\mysql\bin\mysqldump.exe" -u root --single-transaction --routines --triggers --databases inventariostock > "%DESTINO%\inventariostock_%FECHA%.sql"
if errorlevel 1 (
    echo ERROR: no se pudo respaldar la base de datos. Verifique que MySQL este iniciado.
    exit /b 1
)

echo Respaldando las fotos...
robocopy "%PROYECTO%\uploads\productos" "%DESTINO%\fotos" /E /NFL /NDL /NJH /NJS >nul

echo Borrando respaldos de mas de 30 dias...
forfiles /p "%DESTINO%" /m inventariostock_*.sql /d -30 /c "cmd /c del @path" 2>nul

echo Respaldo terminado: %DESTINO%\inventariostock_%FECHA%.sql
