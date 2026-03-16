@echo off
chcp 65001 >nul
echo ====================================
echo   RAMP-BENIN - Serveur de Développement
echo ====================================
echo.

REM Chercher PHP dans le PATH
where php >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set PHP_CMD=php
    goto :start_server
)

REM Chercher PHP dans XAMPP
if exist "C:\xampp\php\php.exe" (
    set PHP_CMD=C:\xampp\php\php.exe
    goto :start_server
)

REM Chercher PHP dans WAMP
if exist "C:\wamp64\bin\php\php8.1.0\php.exe" (
    set PHP_CMD=C:\wamp64\bin\php\php8.1.0\php.exe
    goto :start_server
)

if exist "C:\wamp\bin\php\php8.1.0\php.exe" (
    set PHP_CMD=C:\wamp\bin\php\php8.1.0\php.exe
    goto :start_server
)

REM Chercher PHP dans Laragon
if exist "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" (
    set PHP_CMD=C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe
    goto :start_server
)

echo [ERREUR] PHP n'est pas trouvé
echo.
echo Veuillez :
echo 1. Installer PHP et l'ajouter au PATH
echo 2. OU installer XAMPP/WAMP/Laragon
echo 3. OU modifier ce script avec le chemin vers PHP
echo.
echo Emplacements vérifiés :
echo   - PATH système
echo   - C:\xampp\php\php.exe
echo   - C:\wamp64\bin\php\...
echo   - C:\laragon\bin\php\...
echo.
pause
exit /b 1

:start_server
echo [INFO] PHP trouvé : %PHP_CMD%
echo [INFO] Démarrage du serveur PHP...
echo [INFO] URL : http://localhost:8000
echo [INFO] Appuyez sur Ctrl+C pour arrêter le serveur
echo.
echo ====================================
echo.

REM Démarrer le serveur PHP
%PHP_CMD% -S localhost:8000

pause

