<?php
/**
 * Classe UserActivity - Gestion des activités des utilisateurs
 * RAMP-BENIN
 */

class UserActivity {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Enregistrer une participation à une activité
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO user_activities (
                user_id, activity_id, project_id, date_participation,
                heures, description, statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['activity_id'] ?? null,
            $data['project_id'] ?? null,
            $data['date_participation'],
            $data['heures'] ?? 0,
            $data['description'] ?? null,
            $data['statut'] ?? 'planifiee'
        ]) ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Obtenir les activités d'un utilisateur
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT ua.*, a.title as activity_title, p.title as project_title
            FROM user_activities ua
            LEFT JOIN activities a ON ua.activity_id = a.id
            LEFT JOIN projects p ON ua.project_id = p.id
            WHERE ua.user_id = ?
            ORDER BY ua.date_participation DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Calculer le total d'heures d'un utilisateur
     */
    public function getTotalHeures($userId) {
        $stmt = $this->db->prepare("
            SELECT SUM(heures) as total
            FROM user_activities
            WHERE user_id = ? AND statut = 'terminee'
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

