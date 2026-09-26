@echo off
rem QIEOS Database Migrator (Windows)
rem Menjalankan migrasi SQL di database/migrations

where php >nul 2>nul
if %errorlevel%==0 (
    php "%~dp0migrate.php"
) else (
    "C:\xampp\php\php.exe" "%~dp0migrate.php"
)

if errorlevel 1 (
    echo.
    echo Ada migrasi yang gagal. Periksa pesan di atas.
    pause
)