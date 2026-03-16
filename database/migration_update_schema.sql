-- Migration pour améliorer le schéma RAMP-BENIN
-- Ajout des types d'utilisateurs et responsable de projet

-- 1. Mettre à jour la table users pour ajouter les nouveaux types
ALTER TABLE users 
MODIFY COLUMN role ENUM('admin', 'manager', 'user', 'membre_administration', 'stagiaire', 'benevole') DEFAULT 'user';

-- 2. Ajouter le responsable de projet dans la table projects
ALTER TABLE projects 
ADD COLUMN IF NOT EXISTS responsable_projet_id INT NULL AFTER created_by,
ADD FOREIGN KEY (responsable_projet_id) REFERENCES users(id) ON DELETE SET NULL;

-- 3. Créer une table pour les assignations projet-utilisateur (si plusieurs responsables)
CREATE TABLE IF NOT EXISTS project_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('responsable', 'membre_equipe', 'observateur') DEFAULT 'membre_equipe',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_project_user (project_id, user_id),
    INDEX idx_project (project_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. S'assurer que la relation projet-activité est correcte (déjà présente mais vérifions)
-- La table activities a déjà project_id, donc c'est bon

-- 5. Ajouter un index pour améliorer les performances
CREATE INDEX IF NOT EXISTS idx_responsable ON projects(responsable_projet_id);

