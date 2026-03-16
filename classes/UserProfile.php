<?php
/**
 * Classe UserProfile - Gestion des profils utilisateurs (volontaires, stagiaires, bénévoles)
 * RAMP-BENIN
 */

class UserProfile {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir un profil par ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT up.*, u.full_name, u.email, u.role,
                   vp.full_name as valide_par_name,
                   tp.full_name as tuteur_name,
                   p.title as projet_title
            FROM user_profiles up
            LEFT JOIN users u ON up.user_id = u.id
            LEFT JOIN users vp ON up.valide_par = vp.id
            LEFT JOIN users tp ON up.tuteur_id = tp.id
            LEFT JOIN projects p ON up.projet_affectation_id = p.id
            WHERE up.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir le profil d'un utilisateur par user_id et type
     */
    public function getByUserId($userId, $profileType = null) {
        $sql = "
            SELECT up.*, u.full_name, u.email, u.role
            FROM user_profiles up
            LEFT JOIN users u ON up.user_id = u.id
            WHERE up.user_id = ?
        ";
        
        $params = [$userId];
        if ($profileType) {
            $sql .= " AND up.profile_type = ?";
            $params[] = $profileType;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ? $result : null;
    }
    
    /**
     * Créer un profil
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO user_profiles (
                user_id, profile_type, date_debut, date_fin, statut_validation,
                duree_engagement, charte_signee, ecole_universite, niveau_etudes,
                convention_signee, tuteur_id, maitre_de_stage_id, projet_affectation_id, 
                type_engagement, montant_mensuel, duree_stage
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['profile_type'],
            $data['date_debut'] ?? null,
            $data['date_fin'] ?? null,
            $data['statut_validation'] ?? 'en_attente',
            $data['duree_engagement'] ?? null,
            $data['charte_signee'] ?? false,
            $data['ecole_universite'] ?? null,
            $data['niveau_etudes'] ?? null,
            $data['convention_signee'] ?? false,
            $data['tuteur_id'] ?? null,
            $data['maitre_de_stage_id'] ?? null,
            $data['projet_affectation_id'] ?? null,
            $data['type_engagement'] ?? 'ponctuel',
            $data['montant_mensuel'] ?? null,
            $data['duree_stage'] ?? null
        ]) ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Mettre à jour un profil
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'date_debut', 'date_fin', 'statut_validation', 'date_validation',
            'valide_par', 'notes', 'duree_engagement', 'charte_signee',
            'date_charte_signee', 'heures_effectuees', 'raison_fin',
            'ecole_universite', 'niveau_etudes', 'convention_signee',
            'date_convention_signee', 'tuteur_id', 'maitre_de_stage_id', 
            'projet_affectation_id', 'rapport_rendu', 'type_engagement', 
            'badge_reconnaissance', 'montant_mensuel', 'duree_stage'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE user_profiles SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Valider un profil
     */
    public function validate($id, $validePar) {
        return $this->update($id, [
            'statut_validation' => 'valide',
            'date_validation' => date('Y-m-d'),
            'valide_par' => $validePar
        ]);
    }
    
    /**
     * Refuser un profil
     */
    public function refuse($id, $validePar, $notes = null) {
        return $this->update($id, [
            'statut_validation' => 'refuse',
            'date_validation' => date('Y-m-d'),
            'valide_par' => $validePar,
            'notes' => $notes
        ]);
    }
    
    /**
     * Obtenir tous les profils avec filtres
     */
    public function getAll($filters = []) {
        $sql = "
            SELECT up.*, u.full_name, u.email, u.role, u.status as user_status,
                   vp.full_name as valide_par_name,
                   tp.full_name as tuteur_name,
                   p.title as projet_title
            FROM user_profiles up
            LEFT JOIN users u ON up.user_id = u.id
            LEFT JOIN users vp ON up.valide_par = vp.id
            LEFT JOIN users tp ON up.tuteur_id = tp.id
            LEFT JOIN projects p ON up.projet_affectation_id = p.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['profile_type'])) {
            $sql .= " AND up.profile_type = ?";
            $params[] = $filters['profile_type'];
        }
        
        if (!empty($filters['statut_validation'])) {
            $sql .= " AND up.statut_validation = ?";
            $params[] = $filters['statut_validation'];
        }
        
        if (!empty($filters['duree_engagement'])) {
            $sql .= " AND up.duree_engagement = ?";
            $params[] = $filters['duree_engagement'];
        }
        
        $sql .= " ORDER BY up.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Supprimer un profil
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM user_profiles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

