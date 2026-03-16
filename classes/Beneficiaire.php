<?php
/**
 * Classe Beneficiaire - Gestion des bénéficiaires
 * RAMP-BENIN
 */

class Beneficiaire {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir un bénéficiaire par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT b.*, p.title as project_title, p.code as project_code,
                   a.title as activity_title, a.id as activity_id
            FROM beneficiaries b
            LEFT JOIN projects p ON b.project_id = p.id
            LEFT JOIN activities a ON b.activity_id = a.id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir tous les bénéficiaires
     * @param int|null $projectId Filtrer par projet
     * @return array
     */
    public function getAll($projectId = null, $activityId = null) {
        $sql = "
            SELECT b.*, p.title as project_title, p.code as project_code,
                   a.title as activity_title, a.id as activity_id
            FROM beneficiaries b
            LEFT JOIN projects p ON b.project_id = p.id
            LEFT JOIN activities a ON b.activity_id = a.id
        ";
        
        $conditions = [];
        $params = [];
        
        if ($projectId) {
            $conditions[] = "b.project_id = ?";
            $params[] = $projectId;
        }
        
        if ($activityId) {
            $conditions[] = "b.activity_id = ?";
            $params[] = $activityId;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un bénéficiaire
     * @param array $data
     * @return int|false ID du nouveau bénéficiaire
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO beneficiaries (project_id, activity_id, first_name, last_name, gender, date_of_birth, 
                                     phone, email, address, ville_de_provenance, category, registration_date, status, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['project_id'],
            $data['activity_id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['date_of_birth'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['ville_de_provenance'] ?? null,
            $data['category'] ?? 'individual',
            $data['registration_date'] ?? date('Y-m-d'),
            $data['status'] ?? 'active',
            $data['notes'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Mettre à jour un bénéficiaire
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE beneficiaries 
            SET project_id = ?, activity_id = ?, first_name = ?, last_name = ?, gender = ?, date_of_birth = ?, 
                phone = ?, email = ?, address = ?, ville_de_provenance = ?, category = ?, registration_date = ?, status = ?, notes = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['project_id'],
            $data['activity_id'] ?? null,
            $data['first_name'],
            $data['last_name'],
            $data['gender'],
            $data['date_of_birth'] ?? null,
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['address'] ?? null,
            $data['ville_de_provenance'] ?? null,
            $data['category'],
            $data['registration_date'],
            $data['status'],
            $data['notes'] ?? null,
            $id
        ]);
    }
    
    /**
     * Supprimer un bénéficiaire
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM beneficiaries WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtenir les statistiques des bénéficiaires
     * @return array
     */
    public function getStats() {
        $stats = [];
        
        // Total
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM beneficiaries");
        $stats['total'] = $stmt->fetch()['count'];
        
        // Par catégorie
        $stmt = $this->db->query("
            SELECT category, COUNT(*) as count 
            FROM beneficiaries 
            GROUP BY category
        ");
        $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Par genre
        $stmt = $this->db->query("
            SELECT gender, COUNT(*) as count 
            FROM beneficiaries 
            GROUP BY gender
        ");
        $stats['by_gender'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        return $stats;
    }
}

