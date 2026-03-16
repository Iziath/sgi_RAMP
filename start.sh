#!/bin/bash

echo "===================================="
echo "  RAMP-BENIN - Serveur de Développement"
echo "===================================="
echo ""

# Vérifier si PHP est disponible
if ! command -v php &> /dev/null; then
    echo "[ERREUR] PHP n'est pas trouvé"
    echo ""
    echo "Veuillez installer PHP ou l'ajouter au PATH"
    exit 1
fi

echo "[INFO] Démarrage du serveur PHP..."
echo "[INFO] URL : http://localhost:8000"
echo "[INFO] Appuyez sur Ctrl+C pour arrêter le serveur"
echo ""

# Démarrer le serveur PHP
php -S localhost:8000

