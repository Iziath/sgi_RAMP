# Configuration pour sgi.ramp-afrique.org

## ✅ Modifications effectuées

### 1. **Configuration Base de Données (Production)**
- Fichier: `config/database.php`
- Base de données en ligne activée:
  - **Hôte**: node30-eu.n0c.com
  - **Base**: zgfsqhef_Base
  - **Utilisateur**: zgfsqhef_Iziath
  - **Password**: b*4uiusSHTPXdRu

### 2. **Base URL mise à jour**
- Fichiers modifiés:
  - `config/config.php`
  - `config/constants.php`
- Nouvelle URL: `https://sgi.ramp-afrique.org`

### 3. **Redirection HTTPS**
- `.htaccess` configuré pour forcer HTTPS automatiquement
- Port 443 utilisé par défaut (SSL/TLS)

### 4. **Table Documents créée**
- La table `documents` a été créée dans la base de données
- Module Documents complètement fonctionnel

---

## 📋 Prochaines étapes sur l'hébergeur

### Étape 1: Importer la base de données
1. Connectez-vous à cPanel de votre hébergeur
2. Allez dans **phpMyAdmin**
3. Sélectionnez la base de données `zgfsqhef_Base`
4. Importez le fichier SQL: `/database/ramp_benin_export.sql`
5. Vérifiez que toutes les tables sont créées

### Étape 2: Télécharger les fichiers
1. Connectez-vous via FTP/SFTP
2. Téléchargez tous les fichiers du projet vers le répertoire public_html
3. Assurez-vous que:
   - Les permissions des dossiers `/uploads/` sont **755**
   - Les permissions des dossiers `/uploads/documents/` sont **755**
   - Les fichiers PHP ont les permissions **644**

### Étape 3: Vérifier la configuration SSL
1. Vérifiez que SSL/TLS est activé sur le domaine `sgi.ramp-afrique.org`
2. Le certificat doit être valide (Let's Encrypt gratuit)
3. La redirection HTTP → HTTPS fonctionne automatiquement via `.htaccess`

### Étape 4: Tester le site
1. Accédez à: `https://sgi.ramp-afrique.org`
2. Testez:
   - ✅ Connexion/Déconnexion
   - ✅ Module Documents
   - ✅ Création d'activités
   - ✅ Formulaires CRUD
   - ✅ Imports/Exports

---

## 🔐 Configuration de Sécurité

Le `.htaccess` inclut:
- ✅ Redirection HTTPS obligatoire
- ✅ Protection contre l'affichage des répertoires
- ✅ Blocage d'accès aux dossiers sensibles (config, classes, database)
- ✅ Compression GZIP des assets
- ✅ Cache des ressources statiques
- ✅ En-têtes de sécurité (HSTS, X-Frame-Options, etc.)

---

## ⚠️ En cas de problème

### Les fichiers se téléchargent au lieu de s'afficher
- Vérifiez que PHP est activé sur l'hébergeur
- Vérifiez l'extension `.php` dans les paramètres de l'hébergeur

### Erreur "Table doesn't exist"
- Vérifiez que vous avez importé la base de données complète
- Exécutez les migrations: `database/migration_documents.sql`

### Le site ne s'affiche pas
- Vérifiez les droits d'accès au répertoire (755 pour les dossiers)
- Vérifiez les logs d'erreur PHP dans cPanel
- Assurez-vous que `mod_rewrite` est activé

### Erreur "Permission denied" sur les uploads
- Changez les permissions à 755 pour `/uploads/` et `/uploads/documents/`
- Assurez-vous que le serveur web peut écrire dans ces dossiers

---

## 📝 Configuration Locale (Développement)

Pour revenir au développement local, modifiez `config/database.php`:

```php
// Décommentez:
define('DB_HOST', 'localhost');
define('DB_NAME', 'ramp_benin');
define('DB_USER', 'root');
define('DB_PASS', '');

// Et commentez les paramètres production
```

Puis modifiez `config/config.php` et `config/constants.php`:
```php
define('BASE_URL', 'http://localhost:8000');
```

---

## 🚀 Support

Pour toute question ou problème, vérifiez:
1. Les logs dans `cPanel > Error Log`
2. Les permissions des fichiers/dossiers
3. La version PHP (8.3+ recommandée)
4. L'activation des extensions: PDO, MySQL, mbstring
