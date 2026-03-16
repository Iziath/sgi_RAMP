<?php
/**
 * Classe UserEvaluation - Gestion des évaluations
 * RAMP-BENIN
 */

class UserEvaluation {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer une évaluation
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO user_evaluations (
                user_id, profile_id, type_evaluation, periode_debut, periode_fin,
                evaluateur_id, note_globale, points_forts, points_amelioration,
                recommandations, date_evaluation, statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['profile_id'],
            $data['type_evaluation'],
            $data['periode_debut'],
            $data['periode_fin'],
            $data['evaluateur_id'],
            $data['note_globale'] ?? null,
            $data['points_forts'] ?? null,
            $data['points_amelioration'] ?? null,
            $data['recommandations'] ?? null,
            $data['date_evaluation'] ?? date('Y-m-d'),
            $data['statut'] ?? 'brouillon'
        ]) ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Obtenir les évaluations d'un utilisateur
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT e.*, u.full_name as evaluateur_name
            FROM user_evaluations e
            LEFT JOIN users u ON e.evaluateur_id = u.id
            WHERE e.user_id = ?
            ORDER BY e.date_evaluation DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Finaliser une évaluation
     */
    public function finalize($id) {
        $stmt = $this->db->prepare("UPDATE user_evaluations SET statut = 'finalisee' WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

