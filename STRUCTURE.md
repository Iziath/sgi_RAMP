# Structure du Projet RAMP-BENIN

## 📁 Structure Complète

```
ramp-benin/
├── config/
│   ├── database.php          # Configuration DB
│   ├── database.example.php   # Exemple de configuration
│   ├── constants.php         # Constantes globales
│   └── config.php            # Ancien fichier (peut être supprimé)
├── includes/
│   ├── header.php            # En-tête commun
│   ├── footer.php            # Pied de page commun
│   ├── nav.php               # Menu de navigation
│   ├── functions.php         # Fonctions utilitaires
│   ├── auth.php              # Fonctions d'authentification
│   └── autoload.php          # Autoloader des classes
├── classes/
│   ├── Database.php          # Classe de connexion DB (Singleton)
│   ├── User.php              # Gestion utilisateurs
│   ├── Projet.php            # Gestion projets
│   ├── Activite.php          # Gestion activités
│   ├── Beneficiaire.php      # Gestion bénéficiaires
│   └── Partenaire.php        # Gestion partenaires
├── auth/
│   ├── login.php             # Page de connexion
│   ├── register.php          # Page d'inscription
│   ├── logout.php            # Déconnexion
│   └── check_auth.php        # Vérification session
├── pages/
│   ├── dashboard.php         # Tableau de bord
│   ├── projets/
│   │   ├── index.php         # Liste des projets
│   │   ├── create.php        # Créer un projet
│   │   ├── edit.php          # Modifier un projet
│   │   ├── view.php          # Voir un projet
│   │   └── delete.php        # Supprimer un projet
│   ├── activites/
│   │   ├── index.php         # Liste des activités
│   │   ├── create.php        # Créer une activité
│   │   ├── edit.php          # Modifier une activité
│   │   ├── view.php          # Voir une activité
│   │   └── delete.php        # Supprimer une activité
│   ├── beneficiaires/
│   │   ├── index.php         # Liste des bénéficiaires
│   │   ├── create.php        # Créer un bénéficiaire
│   │   ├── edit.php          # Modifier un bénéficiaire
│   │   ├── view.php          # Voir un bénéficiaire
│   │   └── delete.php        # Supprimer un bénéficiaire
│   └── partenaires/
│       ├── index.php         # Liste des partenaires
│       ├── create.php        # Créer un partenaire
│       ├── edit.php          # Modifier un partenaire
│       ├── view.php          # Voir un partenaire
│       └── delete.php        # Supprimer un partenaire
├── api/
│   ├── stats.php             # API pour statistiques
│   └── ajax_handler.php      # Gestion requêtes AJAX
├── assets/
│   ├── css/
│   │   └── style.css         # Styles personnalisés
│   ├── js/
│   │   └── main.js          # Scripts personnalisés
│   └── images/               # Images (vide pour l'instant)
├── database/
│   └── schema.sql            # Schéma de base de données
├── index.php                 # Page d'accueil (redirection)
├── install.php               # Script d'installation
├── .htaccess                 # Configuration Apache
├── .gitignore                # Fichiers à ignorer
├── README.md                 # Documentation principale
└── STRUCTURE.md              # Ce fichier (documentation structure)
```

## 🎯 Architecture POO

### Classes Principales

1. **Database** (Singleton)
   - Gestion unique de la connexion PDO
   - Méthode `getInstance()` pour obtenir l'instance

2. **User**
   - `authenticate()` - Authentification
   - `getById()` - Obtenir un utilisateur
   - `getAll()` - Liste des utilisateurs
   - `create()` - Créer un utilisateur
   - `update()` - Mettre à jour
   - `delete()` - Supprimer

3. **Projet**
   - `getById()` - Obtenir un projet
   - `getAll()` - Liste des projets
   - `create()` - Créer un projet
   - `update()` - Mettre à jour
   - `delete()` - Supprimer
   - `getStats()` - Statistiques

4. **Activite**
   - `getById()` - Obtenir une activité
   - `getAll($projectId)` - Liste (filtrée par projet)
   - `create()` - Créer une activité
   - `update()` - Mettre à jour
   - `delete()` - Supprimer
   - `getStats()` - Statistiques

5. **Beneficiaire**
   - `getById()` - Obtenir un bénéficiaire
   - `getAll($projectId)` - Liste (filtrée par projet)
   - `create()` - Créer un bénéficiaire
   - `update()` - Mettre à jour
   - `delete()` - Supprimer
   - `getStats()` - Statistiques

6. **Partenaire**
   - `getById()` - Obtenir un partenaire
   - `getAll()` - Liste des partenaires
   - `create()` - Créer un partenaire
   - `update()` - Mettre à jour
   - `delete()` - Supprimer
   - `getByProject($projectId)` - Partenaires d'un projet
   - `getStats()` - Statistiques

## 🔐 Authentification

- **auth/login.php** - Page de connexion
- **auth/register.php** - Page d'inscription (optionnelle)
- **auth/logout.php** - Déconnexion
- **auth/check_auth.php** - Vérification de session (à inclure dans les pages protégées)

## 📊 API

- **api/stats.php** - Retourne les statistiques en JSON
- **api/ajax_handler.php** - Gère les requêtes AJAX (get_project, update_progress, etc.)

## 🎨 Assets

- **assets/css/style.css** - Styles avec charte graphique
- **assets/js/main.js** - Scripts JavaScript personnalisés

## 📝 Notes Importantes

- Toutes les classes utilisent PDO avec requêtes préparées
- L'autoloader charge automatiquement les classes depuis `classes/`
- Les constantes sont définies dans `config/constants.php`
- La navigation est séparée dans `includes/nav.php`
- Chaque module (projets, activités, etc.) a ses propres pages CRUD
- Les anciens fichiers à la racine (`activities.php`, `projects.php`, etc.) peuvent être supprimés si vous utilisez uniquement la nouvelle structure

## 🔄 Flux d'Utilisation

1. **Connexion** : `auth/login.php` → `pages/dashboard.php`
2. **Navigation** : Via `includes/nav.php` dans toutes les pages
3. **CRUD** : Chaque module a ses propres pages (index, create, edit, view, delete)
4. **API** : Endpoints JSON dans `api/` pour les requêtes AJAX

## ⚠️ Fichiers à ne pas supprimer

- `config/constants.php` - Essentiel
- `config/database.php` - Essentiel
- `includes/autoload.php` - Essentiel pour charger les classes
- `classes/*.php` - Toutes les classes
- `database/schema.sql` - Schéma de base de données

