# RAMP-BENIN - Plateforme de Gestion de Projets

## 📋 Vue d'ensemble

RAMP-BENIN est une plateforme web complète de gestion de projets pour organisations, permettant le suivi des activités, bénéficiaires et partenaires avec un design épuré et moderne.

## 🎨 Charte Graphique

### Palette de Couleurs
- **Blanc** : #FFFFFF - Fond principal, cartes
- **Noir** : #000000 - Texte principal, titres
- **Bleu** : #5A6AB2 - Éléments principaux, boutons primaires
- **Vert** : #779D2E - Accents, validations, statuts actifs

### Principes de Design
- Design épuré et minimaliste
- Espaces blancs généreux
- Typographie claire et lisible
- Contrastes nets entre les sections

## 🏗️ Architecture Technique

### Stack Technologique

#### Frontend
- HTML5 / CSS3
- Vanilla JavaScript
- jQuery (via CDN)
- Tailwind CSS (via CDN) pour le styling
- Chart.js pour les graphiques
- DataTables pour les tableaux interactifs

#### Backend
- PHP 8.1+
- MySQL 8.0+ comme base de données
- PDO pour les requêtes sécurisées
- Sessions PHP pour l'authentification
- Password_hash/Password_verify pour les mots de passe

## 📦 Installation

### Prérequis
- PHP 8.1 ou supérieur
- MySQL 8.0 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PDO MySQL activée

### Étapes d'installation

1. **Cloner ou télécharger le projet**
   ```bash
   cd /chemin/vers/votre/serveur/web
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Modifier les paramètres dans `config/database.php` :
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'ramp_benin');
     define('DB_USER', 'votre_utilisateur');
     define('DB_PASS', 'votre_mot_de_passe');
     ```

3. **Importer le schéma de base de données**
   ```bash
   mysql -u votre_utilisateur -p ramp_benin < database/schema.sql
   ```
   Ou via phpMyAdmin : importer le fichier `database/schema.sql`

4. **Configurer l'URL de base**
   - Modifier `BASE_URL` dans `config/config.php` :
     ```php
     define('BASE_URL', 'http://localhost/ramp-benin');
     ```

5. **Configurer les permissions**
   - Assurez-vous que le serveur web a les permissions de lecture sur tous les fichiers
   - Les sessions PHP doivent être activées

6. **Accéder à l'application**
   - Ouvrir votre navigateur et aller à : `https://sgi.ramp-afrique.org`
   - Compte par défaut :
     - **Nom d'utilisateur** : `admin`
     - **Mot de passe** : `admin123`

## 📁 Structure du Projet

```
ramp-benin/
├── assets/
│   ├── css/
│   │   └── style.css          # Styles principaux
│   └── js/
│       └── main.js            # Scripts JavaScript
├── config/
│   ├── config.php            # Configuration générale
│   └── database.php          # Configuration base de données
├── database/
│   └── schema.sql            # Schéma de base de données
├── includes/
│   ├── auth.php              # Fonctions d'authentification
│   ├── functions.php         # Fonctions utilitaires
│   ├── header.php            # En-tête commun
│   └── footer.php            # Pied de page commun
├── activities.php            # Gestion des activités
├── beneficiaries.php         # Gestion des bénéficiaires
├── dashboard.php             # Tableau de bord
├── index.php                 # Page d'accueil (redirection)
├── login.php                 # Page de connexion
├── logout.php                # Déconnexion
├── partners.php              # Gestion des partenaires
├── projects.php              # Gestion des projets
└── README.md                 # Ce fichier
```

## 🔐 Sécurité

- Mots de passe hashés avec `password_hash()`
- Requêtes préparées avec PDO pour éviter les injections SQL
- Échappement des données avec `htmlspecialchars()`
- Validation des entrées utilisateur
- Sessions sécurisées
- Protection CSRF (à implémenter selon besoins)

## 📝 Fonctionnalités

### Gestion des Projets
- Création, modification, suppression de projets
- Suivi du budget et de la progression
- Gestion des statuts (planification, actif, terminé, annulé)
- Association avec des organisations

### Gestion des Activités
- Création et suivi des activités par projet
- Dates prévues et réelles
- Suivi de la progression
- Gestion du budget par activité

### Gestion des Bénéficiaires
- Enregistrement des bénéficiaires
- Association avec les projets
- Catégorisation (individuel, groupe, communauté)
- Suivi des informations de contact

### Gestion des Partenaires
- Enregistrement des partenaires
- Types de partenaires (gouvernement, ONG, privé, international)
- Informations de contact complètes
- Association avec les projets

### Tableau de Bord
- Statistiques générales
- Graphiques de visualisation
- Liste des projets récents
- Activités en cours

## 🔧 Configuration

### Modifier les paramètres de session
Dans `config/config.php` :
```php
define('SESSION_LIFETIME', 3600); // Durée en secondes
```

### Modifier la timezone
Dans `config/config.php` :
```php
date_default_timezone_set('Africa/Porto-Novo');
```

## 🐛 Dépannage

### Problème de connexion à la base de données
- Vérifier les paramètres dans `config/database.php`
- Vérifier que MySQL est démarré
- Vérifier que l'utilisateur a les permissions nécessaires

### Problème de session
- Vérifier que les sessions PHP sont activées
- Vérifier les permissions d'écriture du dossier de sessions

### Erreur 404
- Vérifier la configuration de `BASE_URL`
- Vérifier la configuration du serveur web (mod_rewrite si nécessaire)

## 📄 Licence

Ce projet est développé pour RAMP-BENIN.

## 👥 Support

Pour toute question ou problème, veuillez contacter l'équipe de développement.

---

**Version** : 1.0.0  
**Dernière mise à jour** : 2024

