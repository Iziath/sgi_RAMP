# Script pour trouver et configurer PHP
Write-Host "====================================" -ForegroundColor Cyan
Write-Host "  Recherche de PHP..." -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Emplacements courants à vérifier
$searchPaths = @(
    "C:\php",
    "C:\xampp\php",
    "C:\wamp64\bin\php",
    "C:\wamp\bin\php",
    "C:\laragon\bin\php",
    "$env:ProgramFiles\PHP",
    "$env:ProgramFiles(x86)\PHP",
    "$env:LOCALAPPDATA\Programs\PHP"
)

$foundPhp = @()

Write-Host "Recherche en cours..." -ForegroundColor Yellow

# Chercher dans les emplacements courants
foreach ($path in $searchPaths) {
    if (Test-Path $path) {
        $phpExe = Join-Path $path "php.exe"
        if (Test-Path $phpExe) {
            $foundPhp += $phpExe
            Write-Host "[TROUVÉ] $phpExe" -ForegroundColor Green
        } else {
            # Chercher dans les sous-dossiers
            $subDirs = Get-ChildItem -Path $path -Directory -ErrorAction SilentlyContinue
            foreach ($subDir in $subDirs) {
                $phpExe = Join-Path $subDir.FullName "php.exe"
                if (Test-Path $phpExe) {
                    $foundPhp += $phpExe
                    Write-Host "[TROUVÉ] $phpExe" -ForegroundColor Green
                }
            }
        }
    }
}

# Recherche approfondie sur le disque C: (peut être long)
Write-Host ""
Write-Host "Recherche approfondie sur C:\ (peut prendre du temps)..." -ForegroundColor Yellow
$deepSearch = Get-ChildItem -Path "C:\" -Filter "php.exe" -Recurse -ErrorAction SilentlyContinue -Depth 4 | Select-Object -First 10
foreach ($php in $deepSearch) {
    if ($php.FullName -notin $foundPhp) {
        $foundPhp += $php.FullName
        Write-Host "[TROUVÉ] $($php.FullName)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "====================================" -ForegroundColor Cyan

if ($foundPhp.Count -eq 0) {
    Write-Host "[ERREUR] PHP n'a pas été trouvé !" -ForegroundColor Red
    Write-Host ""
    Write-Host "Solutions :" -ForegroundColor Yellow
    Write-Host "1. Vérifiez que PHP est bien installé" -ForegroundColor White
    Write-Host "2. Installez XAMPP : https://www.apachefriends.org" -ForegroundColor White
    Write-Host "3. Ou installez PHP standalone : https://windows.php.net/download/" -ForegroundColor White
} else {
    Write-Host "[SUCCESS] PHP trouvé : $($foundPhp.Count) installation(s)" -ForegroundColor Green
    Write-Host ""
    
    # Afficher toutes les installations trouvées
    for ($i = 0; $i -lt $foundPhp.Count; $i++) {
        Write-Host "$($i+1). $($foundPhp[$i])" -ForegroundColor Cyan
        
        # Tester la version
        try {
            $version = & $foundPhp[$i] -v 2>&1 | Select-Object -First 1
            Write-Host "   Version : $version" -ForegroundColor Gray
        } catch {
            Write-Host "   (Impossible de lire la version)" -ForegroundColor Gray
        }
    }
    
    Write-Host ""
    Write-Host "====================================" -ForegroundColor Cyan
    Write-Host "  Configuration du PATH" -ForegroundColor Cyan
    Write-Host "====================================" -ForegroundColor Cyan
    Write-Host ""
    
    # Proposer d'ajouter au PATH
    if ($foundPhp.Count -eq 1) {
        $phpPath = Split-Path $foundPhp[0] -Parent
        Write-Host "Voulez-vous ajouter PHP au PATH ?" -ForegroundColor Yellow
        Write-Host "Chemin : $phpPath" -ForegroundColor Cyan
        Write-Host ""
        $response = Read-Host "Oui (O) / Non (N)"
        
        if ($response -eq "O" -or $response -eq "o") {
            try {
                # Ajouter au PATH utilisateur (ne nécessite pas admin)
                $currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
                if ($currentPath -notlike "*$phpPath*") {
                    [Environment]::SetEnvironmentVariable("Path", "$currentPath;$phpPath", "User")
                    Write-Host "[SUCCESS] PHP ajouté au PATH utilisateur !" -ForegroundColor Green
                    Write-Host "[INFO] Redémarrez PowerShell pour que les changements prennent effet" -ForegroundColor Yellow
                } else {
                    Write-Host "[INFO] PHP est déjà dans le PATH" -ForegroundColor Yellow
                }
            } catch {
                Write-Host "[ERREUR] Impossible d'ajouter au PATH : $_" -ForegroundColor Red
                Write-Host "[INFO] Vous pouvez l'ajouter manuellement :" -ForegroundColor Yellow
                Write-Host "  1. Clic droit sur 'Ce PC' -> Propriétés" -ForegroundColor White
                Write-Host "  2. Paramètres système avancés" -ForegroundColor White
                Write-Host "  3. Variables d'environnement" -ForegroundColor White
                Write-Host "  4. Path -> Modifier -> Ajouter : $phpPath" -ForegroundColor White
            }
        }
    } else {
        Write-Host "Plusieurs installations de PHP trouvées." -ForegroundColor Yellow
        Write-Host "Choisissez laquelle utiliser (1-$($foundPhp.Count)) :" -ForegroundColor Yellow
        $choice = Read-Host "Numéro"
        
        if ($choice -match '^\d+$' -and [int]$choice -ge 1 -and [int]$choice -le $foundPhp.Count) {
            $selectedPhp = $foundPhp[[int]$choice - 1]
            $phpPath = Split-Path $selectedPhp -Parent
            Write-Host ""
            Write-Host "Voulez-vous ajouter ce PHP au PATH ?" -ForegroundColor Yellow
            Write-Host "Chemin : $phpPath" -ForegroundColor Cyan
            $response = Read-Host "Oui (O) / Non (N)"
            
            if ($response -eq "O" -or $response -eq "o") {
                try {
                    $currentPath = [Environment]::GetEnvironmentVariable("Path", "User")
                    if ($currentPath -notlike "*$phpPath*") {
                        [Environment]::SetEnvironmentVariable("Path", "$currentPath;$phpPath", "User")
                        Write-Host "[SUCCESS] PHP ajouté au PATH !" -ForegroundColor Green
                        Write-Host "[INFO] Redémarrez PowerShell" -ForegroundColor Yellow
                    } else {
                        Write-Host "[INFO] Déjà dans le PATH" -ForegroundColor Yellow
                    }
                } catch {
                    Write-Host "[ERREUR] Impossible d'ajouter au PATH" -ForegroundColor Red
                }
            }
        }
    }
    
    Write-Host ""
    Write-Host "====================================" -ForegroundColor Cyan
    Write-Host "  Utilisation Immédiate" -ForegroundColor Cyan
    Write-Host "====================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Vous pouvez utiliser PHP immédiatement avec le chemin complet :" -ForegroundColor Yellow
    Write-Host ""
    if ($foundPhp.Count -eq 1) {
        $phpExe = $foundPhp[0]
        Write-Host "  & '$phpExe' -S localhost:8000" -ForegroundColor Green
        Write-Host ""
        Write-Host "Ou lancer le projet directement :" -ForegroundColor Yellow
        Write-Host "  cd C:\Users\Iziath\Desktop\RAMP" -ForegroundColor Cyan
        Write-Host "  & '$phpExe' -S localhost:8000" -ForegroundColor Cyan
    } else {
        Write-Host "  & '$($foundPhp[0])' -S localhost:8000" -ForegroundColor Green
    }
}

Write-Host ""
Read-Host "Appuyez sur Entrée pour quitter"

