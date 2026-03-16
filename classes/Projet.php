<?php
/**
 * Classe Projet - Gestion des projets
 * RAMP-BENIN
 */

class Projet {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir un projet par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, o.name as organization_name, u.full_name as created_by_name
            FROM projects p
            LEFT JOIN organizations o ON p.organization_id = o.id
            LEFT JOIN users u ON p.created_by = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir tous les projets
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("
            SELECT p.*, o.name as organization_name, u.full_name as created_by_name
            FROM projects p
            LEFT JOIN organizations o ON p.organization_id = o.id
            LEFT JOIN users u ON p.created_by = u.id
            ORDER BY p.created_at DESC
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un projet
     * @param array $data
     * @return int|false ID du nouveau projet
     */
    public function create($data) {
        $code = $this->generateCode();
        
        $stmt = $this->db->prepare("
            INSERT INTO projects (organization_id, code, title, description, start_date, end_date, budget, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['organization_id'] ?? 1,
            $code,
            $data['title'],
            $data['description'] ?? null,
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['budget'] ?? 0,
            $data['status'] ?? 'planning',
            $data['created_by']
        ]);
        
        $projectId = $result ? $this->db->lastInsertId() : false;
        
        // Assigner le partenaire si fourni
        if ($projectId && !empty($data['partner_id'])) {
            $this->assignPartner($projectId, $data['partner_id'], $data['partner_role'] ?? 'partenaire');
        }
        
        // Assigner des utilisateurs supplémentaires si fournis
        if ($projectId && !empty($data['user_ids'])) {
            foreach ($data['user_ids'] as $userId) {
                $this->assignUser($projectId, $userId, 'membre_equipe');
            }
        }
        
        return $projectId;
    }
    
    /**
     * Mettre à jour un projet
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE projects 
            SET title = ?, description = ?, start_date = ?, end_date = ?, 
                budget = ?, status = ?, progress = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['start_date'],
            $data['end_date'] ?? null,
            $data['budget'] ?? 0,
            $data['status'],
            $data['progress'] ?? 0,
            $id
        ]);
    }
    
    /**
     * Supprimer un projet
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir les statistiques des projets
     * @return array
     */
    public function getStats() {
        $stats = [];
        
        // Total
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM projects");
        $stats['total'] = $stmt->fetch()['count'];
        
        // Par statut
        $stmt = $this->db->query("
            SELECT status, COUNT(*) as count 
            FROM projects 
            GROUP BY status
        ");
        $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Budget total
        $stmt = $this->db->query("SELECT SUM(budget) as total FROM projects");
        $stats['total_budget'] = $stmt->fetch()['total'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Assigner un partenaire à un projet
     * @param int $projectId
     * @param int $partnerId
     * @param string $role
     * @return bool
     */
    public function assignPartner($projectId, $partnerId, $role = 'partenaire') {
        $stmt = $this->db->prepare("
            INSERT INTO project_partners (project_id, partner_id, role)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE role = ?
        ");
        return $stmt->execute([$projectId, $partnerId, $role, $role]);
    }
    
    /**
     * Assigner un utilisateur à un projet
     * @param int $projectId
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public function assignUser($projectId, $userId, $role = 'membre_equipe') {
        $stmt = $this->db->prepare("
            INSERT INTO project_users (project_id, user_id, role)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE role = ?
        ");
        return $stmt->execute([$projectId, $userId, $role, $role]);
    }
    
    /**
     * Obtenir les utilisateurs assignés à un projet
     * @param int $projectId
     * @return array
     */
    public function getAssignedUsers($projectId) {
        $stmt = $this->db->prepare("
            SELECT u.*, pu.role as project_role
            FROM users u
            INNER JOIN project_users pu ON u.id = pu.user_id
            WHERE pu.project_id = ?
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir les partenaires assignés à un projet
     * @param int $projectId
     * @return array
     */
    public function getAssignedPartners($projectId) {
        $stmt = $this->db->prepare("
            SELECT p.*, pp.role as project_role, pp.contribution
            FROM partners p
            INNER JOIN project_partners pp ON p.id = pp.partner_id
            WHERE pp.project_id = ?
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Supprimer tous les partenaires d'un projet
     * @param int $projectId
     * @return bool
     */
    public function removeAllPartners($projectId) {
        $stmt = $this->db->prepare("DELETE FROM project_partners WHERE project_id = ?");
        return $stmt->execute([$projectId]);
    }
    
    /**
     * Assigner plusieurs partenaires à un projet
     * @param int $projectId
     * @param array $partnerIds
     * @return bool
     */
    public function assignPartners($projectId, $partnerIds) {
        if (empty($partnerIds)) {
            return true;
        }
        
        // Supprimer les anciennes associations
        $this->removeAllPartners($projectId);
        
        // Ajouter les nouvelles associations
        $stmt = $this->db->prepare("
            INSERT INTO project_partners (project_id, partner_id, role)
            VALUES (?, ?, 'partenaire')
        ");
        
        foreach ($partnerIds as $partnerId) {
            if (!$stmt->execute([$projectId, $partnerId])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Assigner plusieurs activités à un projet (mise à jour du project_id)
     * @param int $projectId
     * @param array $activityIds
     * @return bool
     */
    public function assignActivities($projectId, $activityIds) {
        if (empty($activityIds)) {
            return true;
        }
        
        $stmt = $this->db->prepare("UPDATE activities SET project_id = ? WHERE id = ?");
        
        foreach ($activityIds as $activityId) {
            if (!$stmt->execute([$projectId, $activityId])) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Obtenir les IDs des partenaires assignés à un projet
     * @param int $projectId
     * @return array
     */
    public function getAssignedPartnerIds($projectId) {
        $stmt = $this->db->prepare("SELECT partner_id FROM project_partners WHERE project_id = ?");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Obtenir les IDs des activités assignées à un projet
     * @param int $projectId
     * @return array
     */
    public function getAssignedActivityIds($projectId) {
        $stmt = $this->db->prepare("SELECT id FROM activities WHERE project_id = ?");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Générer un code unique pour le projet
     * @return string
     */
    private function generateCode() {
        do {
            $code = 'PROJ-' . strtoupper(uniqid());
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM projects WHERE code = ?");
            $stmt->execute([$code]);
        } while ($stmt->fetchColumn() > 0);
        
        return $code;
    }
}

