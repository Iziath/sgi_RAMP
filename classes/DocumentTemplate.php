<?php
/**
 * Classe DocumentTemplate - Gestion des modèles de documents
 * RAMP-BENIN
 */

class DocumentTemplate {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir les documents obligatoires pour un type de profil
     */
    public function getByProfileType($profileType) {
        $stmt = $this->db->prepare("
            SELECT * FROM document_templates
            WHERE profile_type = ?
            ORDER BY ordre ASC
        ");
        $stmt->execute([$profileType]);
        return $stmt->fetchAll();
    }
    
    /**
     * Vérifier si un utilisateur a tous les documents obligatoires
     */
    public function checkRequiredDocuments($userId, $profileType) {
        $required = $this->getByProfileType($profileType);
        $required = array_filter($required, fn($d) => $d['obligatoire']);
        
        $stmt = $this->db->prepare("
            SELECT type_document FROM user_documents
            WHERE user_id = ? AND type_document IN (
                SELECT type_document FROM document_templates 
                WHERE profile_type = ? AND obligatoire = TRUE
            )
        ");
        $stmt->execute([$userId, $profileType]);
        $uploaded = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $missing = [];
        foreach ($required as $req) {
            if (!in_array($req['type_document'], $uploaded)) {
                $missing[] = $req;
            }
        }
        
        return [
            'complete' => empty($missing),
            'missing' => $missing,
            'uploaded' => count($uploaded),
            'required' => count($required)
        ];
    }
}

