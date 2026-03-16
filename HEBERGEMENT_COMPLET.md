# Guide Complet de Mise en Ligne sur sgi.ramp-afrique.org

## 📋 Résumé des Modifications Effectuées

✅ **Configuration Base URL**: `https://sgi.ramp-afrique.org`
✅ **Base de données production**: Configurée (node30-eu.n0c.com)
✅ **Redirection HTTPS**: Automatique via .htaccess
✅ **Table documents**: Créée et fonctionnelle
✅ **Module documents**: Complètement implémenté

---

## 🚀 Étapes de Mise en Ligne

### ÉTAPE 1: Préparer les fichiers (15 min)

1. **Créer une archive ZIP du projet**
   ```
   - Clic droit sur le dossier RAMP
   - Envoyer vers > Dossier compressé
   - Nommer: RAMP.zip
   ```

2. **Fichiers à vérifier avant upload**
   ```
   ✅ config/database.php (BD production activée)
   ✅ config/config.php (BASE_URL = https://sgi.ramp-afrique.org)
   ✅ config/constants.php (BASE_URL = https://sgi.ramp-afrique.org)
   ✅ .htaccess (Redirection HTTPS)
   ```

---

### ÉTAPE 2: Connexion à l'hébergeur (5 min)

#### Via cPanel

1. **Accédez à cPanel**
   ```
   URL: https://sgi.ramp-afrique.org:2083
   (Ou demandez l'URL à votre hébergeur)
   ```

2. **Identifiants**
   ```
   Utilisateur: [Demander à l'administrateur]
   Mot de passe: [Demander à l'administrateur]
   ```

#### Via FTP

1. **Paramètres FTP**
   ```
   Serveur: ftp.sgi.ramp-afrique.org
   (Ou obtenu dans cPanel > FTP Accounts)
   
   Utilisateur: [Votre utilisateur FTP]
   Mot de passe: [Votre mot de passe FTP]
   
   Port: 21 (FTP) ou 22 (SFTP recommandé)
   ```

2. **Logiciel FTP recommandé**
   - FileZilla (gratuit) https://filezilla-project.org
   - WinSCP (Windows)
   - Cyberduck (Mac)

---

### ÉTAPE 3: Télécharger les fichiers (10-20 min)

#### Avec FileZilla

1. **Se connecter**
   ```
   Fichier > Gestionnaire de sites
   Nouveau site > Saisir les paramètres FTP
   Cliquer: Connecter
   ```

2. **Naviguer vers le bon répertoire**
   ```
   À gauche (Local):  C:\xampp\htdocs\RAMP
   À droite (Serveur): public_html/ (ou /www/)
   ```

3. **Télécharger tous les fichiers**
   ```
   Sélectionner tous les fichiers et dossiers
   Clic droit > Télécharger
   Attendre la fin du transfert
   ```

#### Avec cPanel > File Manager

1. **Ouvrir File Manager**
   - Dans cPanel > File Manager
   - Sélectionner: public_html

2. **Importer l'archive**
   - Bouton: Upload
   - Sélectionner: RAMP.zip
   - Cliquer: Upload

3. **Extraire l'archive**
   - Clic droit sur RAMP.zip
   - Extract > Extraire ici

---

### ÉTAPE 4: Importer la Base de Données (10 min)

1. **Accéder à phpMyAdmin**
   ```
   Via cPanel > phpMyAdmin
   Ou directement: https://sgi.ramp-afrique.org/phpmyadmin
   ```

2. **Sélectionner la base de données**
   ```
   Base: zgfsqhef_Base
   ```

3. **Importer les données**
   ```
   Onglet: Importer
   Choisir un fichier: database/ramp_benin_export.sql
   Cliquer: Exécuter
   ```

4. **Vérifier les tables**
   ```
   ✅ activites
   ✅ beneficiaires
   ✅ documents
   ✅ partenaires
   ✅ projets
   ✅ users
   ✅ Et toutes les autres tables
   ```

---

### ÉTAPE 5: Configurer les Permissions (5 min)

1. **Via FTP (FileZilla)**
   ```
   Pour les dossiers uploads:
   Clic droit > Propriétés du fichier
   Permissions: 755
   
   Pour les fichiers PHP:
   Permissions: 644
   ```

2. **Via cPanel > File Manager**
   ```
   Dossier uploads > Clic droit > Change Permissions
   Cocher: Owner(7), Group(5), Public(5)
   = 755
   ```

**Dossiers critiques à mettre à 755:**
```
/uploads/
/uploads/documents/
```

---

### ÉTAPE 6: Configurer SSL/TLS (IMPORTANT!)

1. **Via cPanel**
   ```
   Rechercher: AutoSSL
   Ou: SSL/TLS Manager
   Vérifier que le certificat est activé pour sgi.ramp-afrique.org
   ```

2. **Vérifier la configuration**
   - Ouvrir: https://sgi.ramp-afrique.org
   - Vérifier: Pas d'avertissement de certificat
   - Vérifier: URL en HTTPS

---

### ÉTAPE 7: Vérifier que mod_rewrite est activé

1. **Via cPanel**
   ```
   Rechercher: Apache Modules
   Ou: Outils de système > Apache Modules
   Chercher: mod_rewrite
   Vérifier: ✅ Activé
   ```

2. **Si mod_rewrite est désactivé**
   - Contacter le support d'hébergement
   - Demander: "Activation de mod_rewrite pour Apache"

---

### ÉTAPE 8: Tester le Site (10 min)

1. **Accédez au site**
   ```
   https://sgi.ramp-afrique.org
   ```

2. **Tests à effectuer**
   ```
   ✅ Page d'accueil s'affiche
   ✅ Connexion/Déconnexion
   ✅ Navigation dans les menus
   ✅ Module Activités
   ✅ Module Bénéficiaires
   ✅ Module Documents (nouveau)
   ✅ Module Projets
   ✅ Module Partenaires
   ✅ Module Utilisateurs
   ✅ Imports/Exports
   ```

3. **Vérifier la console du navigateur**
   ```
   F12 > Console
   Vérifier: Pas d'erreurs 404 sur les assets
   Tous les CSS/JS doivent charger correctement
   ```

---

## 🔍 Dépannage

### Problème: "404 Not Found"

**Cause possible**: mod_rewrite non activé
```
Solution: Contacter le support d'hébergement
Demander: Activation de mod_rewrite
```

### Problème: Erreur "Table doesn't exist"

**Cause possible**: Base de données non importée
```
Solution:
1. Aller dans cPanel > phpMyAdmin
2. Sélectionner la base zgfsqhef_Base
3. Importer le fichier database/ramp_benin_export.sql
```

### Problème: Erreur "Connection refused"

**Cause possible**: Les identifiants BD sont incorrects
```
Solution:
1. Vérifier config/database.php
2. Contacter le support pour les bons identifiants
3. Mettre à jour DB_HOST, DB_USER, DB_PASS
```

### Problème: Erreur "Permission denied"

**Cause possible**: Permissions des dossiers /uploads
```
Solution:
1. Aller dans cPanel > File Manager
2. Clic droit sur /uploads > Change Permissions
3. Mettre à 755 (rwxr-xr-x)
```

### Problème: "HTTPS redirect loop"

**Cause possible**: Configuration .htaccess conflictuelle
```
Solution:
1. Vérifier que le certificat SSL est installé
2. Vérifier que .htaccess a la redirection HTTPS correcte
3. Contacter le support d'hébergement
```

### Problème: Les fichiers se téléchargent au lieu de s'afficher

**Cause possible**: PHP non activé sur le serveur
```
Solution:
1. Vérifier auprès du support que PHP 8.0+ est activé
2. Vérifier que les extensions PDO, MySQL sont chargées
```

---

## 📊 Vérification en Ligne

Vous pouvez vérifier la configuration à tout moment en accédant à:

```
https://sgi.ramp-afrique.org/verify_production.php
```

Ce script affichera:
- ✅ Configuration BASE_URL
- ✅ Paramètres de base de données
- ✅ État de la connexion BD
- ✅ Permissions des dossiers
- ✅ Extensions PHP chargées
- ✅ Configuration HTTPS

---

## 📝 Checklist Finale

Avant de considérer le site comme "en ligne":

```
☐ Tous les fichiers téléchargés via FTP
☐ Base de données importée et tables vérifiées
☐ Permissions configurées (755 pour /uploads)
☐ SSL/TLS activé sur le domaine
☐ mod_rewrite activé sur Apache
☐ Page d'accueil accessible en HTTPS
☐ Login/Logout fonctionnels
☐ Module Documents accessible et fonctionnel
☐ Imports/Exports testés
☐ Tous les formulaires testés
☐ verify_production.php affiche tous les ✅
```

---

## 🆘 Support

En cas de problème non listé:

1. **Vérifier les logs**
   ```
   cPanel > Error Log
   Chercher les messages d'erreur PHP
   ```

2. **Contacter le support d'hébergement**
   ```
   Fournir:
   - Le message d'erreur exact
   - L'URL du problème
   - Les étapes pour reproduire
   ```

3. **Documents fournis**
   ```
   - Ce guide (HEBERGEMENT_COMPLET.md)
   - Configuration production (CONFIGURATION_PRODUCTION.md)
   - Script de vérification (verify_production.php)
   ```

---

## 🎉 Félicitations!

Vous avez mis en ligne RAMP-BENIN sur **sgi.ramp-afrique.org**! 

Le projet est maintenant accessible à tous les utilisateurs avec:
- ✅ HTTPS sécurisé
- ✅ Base de données en ligne
- ✅ Module Documents complet
- ✅ Tous les modules fonctionnels

**Bon fonctionnement du site!** 🚀
