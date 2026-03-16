# Script PowerShell pour lancer RAMP-BENIN
# Détecte automatiquement PHP

Write-Host "====================================" -ForegroundColor Cyan
Write-Host "  RAMP-BENIN - Lancement du Projet" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Aller dans le dossier du script
Set-Location $PSScriptRoot
Write-Host "[INFO] Dossier : $PWD" -ForegroundColor Yellow
Write-Host ""

# Chercher PHP dans les emplacements courants
$phpPaths = @(
    "php",  # Dans le PATH
    "C:\xampp\php\php.exe",
    "C:\wamp64\bin\php\php8.1.0\php.exe",
    "C:\wamp64\bin\php\php8.2.0\php.exe",
    "C:\wamp64\bin\php\php8.3.0\php.exe",
    "C:\wamp\bin\php\php8.1.0\php.exe",
    "C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe",
    "C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php.exe",
    "C:\Program Files\PHP\php.exe",
    "C:\php\php.exe"
)

$php = $null
foreach ($path in $phpPaths) {
    try {
        if ($path -eq "php") {
            # Tester si php est dans le PATH
            $result = Get-Command php -ErrorAction SilentlyContinue
            if ($result) {
                $php = "php"
                break
            }
        } elseif (Test-Path $path) {
            $php = $path
            break
        }
    } catch {
        continue
    }
}

if ($php) {
    Write-Host "[SUCCESS] PHP trouvé : $php" -ForegroundColor Green
    
    # Afficher la version
    Write-Host "[INFO] Version PHP :" -ForegroundColor Yellow
    if ($php -eq "php") {
        php -v
    } else {
        & $php -v
    }
    Write-Host ""
    
    Write-Host "====================================" -ForegroundColor Cyan
    Write-Host "[INFO] Démarrage du serveur..." -ForegroundColor Yellow
    Write-Host "[INFO] URL : http://localhost:8000" -ForegroundColor Green
    Write-Host "[INFO] Appuyez sur Ctrl+C pour arrêter" -ForegroundColor Yellow
    Write-Host "====================================" -ForegroundColor Cyan
    Write-Host ""
    
    # Lancer le serveur
    if ($php -eq "php") {
        php -S localhost:8000
    } else {
        & $php -S localhost:8000
    }
} else {
    Write-Host "[ERREUR] PHP n'est pas trouvé !" -ForegroundColor Red
    Write-Host ""
    Write-Host "Solutions :" -ForegroundColor Yellow
    Write-Host "1. Installer XAMPP : https://www.apachefriends.org" -ForegroundColor White
    Write-Host "2. Installer WAMP : https://www.wampserver.com" -ForegroundColor White
    Write-Host "3. Installer PHP standalone : https://windows.php.net/download/" -ForegroundColor White
    Write-Host "4. Utiliser le chemin complet vers php.exe" -ForegroundColor White
    Write-Host ""
    Write-Host "Emplacements vérifiés :" -ForegroundColor Yellow
    foreach ($path in $phpPaths) {
        if ($path -ne "php") {
            Write-Host "  - $path" -ForegroundColor Gray
        }
    }
    Write-Host ""
    Read-Host "Appuyez sur Entrée pour quitter"
}

