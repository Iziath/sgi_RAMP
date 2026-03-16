<?php
/**
 * Classe Activite - Gestion des activités
 * RAMP-BENIN
 */

class Activite {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir une activité par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT a.*, p.title as project_title, p.code as project_code, u.full_name as created_by_name
            FROM activities a
            LEFT JOIN projects p ON a.project_id = p.id
            LEFT JOIN users u ON a.created_by = u.id
            WHERE a.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir toutes les activités
     * @param int|null $projectId Filtrer par projet
     * @return array
     */
    public function getAll($projectId = null) {
        $sql = "
            SELECT a.*, p.title as project_title, p.code as project_code, u.full_name as created_by_name
            FROM activities a
            LEFT JOIN projects p ON a.project_id = p.id
            LEFT JOIN users u ON a.created_by = u.id
        ";
        
        if ($projectId) {
            $sql .= " WHERE a.project_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$projectId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Créer une activité
     * @param array $data
     * @return int|false ID de la nouvelle activité
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO activities (project_id, title, description, planned_start_date, planned_end_date, 
                                  actual_start_date, actual_end_date, status, progress, budget, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            !empty($data['project_id']) ? $data['project_id'] : null,
            $data['title'],
            $data['description'] ?? null,
            $data['planned_start_date'] ?? null,
            $data['planned_end_date'] ?? null,
            $data['actual_start_date'] ?? null,
            $data['actual_end_date'] ?? null,
            $data['status'] ?? 'planned',
            $data['progress'] ?? 0,
            $data['budget'] ?? 0,
            $data['created_by']
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Mettre à jour une activité
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE activities 
            SET project_id = ?, title = ?, description = ?, planned_start_date = ?, planned_end_date = ?,
                actual_start_date = ?, actual_end_date = ?, status = ?, progress = ?, budget = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            !empty($data['project_id']) ? $data['project_id'] : null,
            $data['title'],
            $data['description'] ?? null,
            $data['planned_start_date'] ?? null,
            $data['planned_end_date'] ?? null,
            $data['actual_start_date'] ?? null,
            $data['actual_end_date'] ?? null,
            $data['status'],
            $data['progress'] ?? 0,
            $data['budget'] ?? 0,
            $id
        ]);
    }
    
    /**
     * Supprimer une activité
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM activities WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir les statistiques des activités
     * @return array
     */
    public function getStats() {
        $stats = [];
        
        // Total
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM activities");
        $stats['total'] = $stmt->fetch()['count'];
        
        // Par statut
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM activities 
            GROUP BY status
        ");
        $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Activités en cours
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM activities WHERE status = 'in_progress'");
        $stats['in_progress'] = $stmt->fetch()['count'];
        
        return $stats;
    }
}

