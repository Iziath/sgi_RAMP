<?php
/**
 * Classe Partenaire - Gestion des partenaires
 * RAMP-BENIN
 */

class Partenaire {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir un partenaire par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM partners WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir tous les partenaires
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM partners ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un partenaire
     * @param array $data
     * @return int|false ID du nouveau partenaire
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO partners (name, type, contact_person, phone, email, address, website, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['type'],
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['website'] ?? null,
            $data['description'] ?? null,
            $data['status'] ?? 'active'
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Mettre à jour un partenaire
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE partners 
            SET name = ?, type = ?, contact_person = ?, phone = ?, email = ?, 
                address = ?, website = ?, description = ?, status = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['type'],
            $data['contact_person'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['website'] ?? null,
            $data['description'] ?? null,
            $data['status'],
            $id
        ]);
    }
    
    /**
     * Supprimer un partenaire
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM partners WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir les partenaires d'un projet
     * @param int $projectId
     * @return array
     */
    public function getByProject($projectId) {
        $stmt = $this->db->prepare("
            SELECT p.*, pp.role, pp.contribution, pp.start_date, pp.end_date
            FROM partners p
            INNER JOIN project_partners pp ON p.id = pp.partner_id
            WHERE pp.project_id = ?
        ");
        $stmt->execute([$projectId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtenir les statistiques des partenaires
     * @return array
     */
    public function getStats() {
        $stats = [];
        
        // Total
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM partners WHERE status = 'active'");
        $stats['total'] = $stmt->fetch()['count'];
        
        // Par type
        $stmt = $this->db->query("
            SELECT type, COUNT(*) as count 
            FROM partners 
            WHERE status = 'active'
            GROUP BY type
        ");
        $stats['by_type'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        return $stats;
    }
}

