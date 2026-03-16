<?php
/**
 * Classe UserPayment - Gestion des règlements/paiements
 * RAMP-BENIN
 */

class UserPayment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Créer un paiement
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO user_payments (
                user_id, profile_id, montant, periode, type_paiement,
                statut, date_paiement, mode_paiement, reference_paiement, notes, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['profile_id'],
            $data['montant'],
            $data['periode'],
            $data['type_paiement'] ?? 'mensuel',
            $data['statut'] ?? 'en_attente',
            $data['date_paiement'] ?? null,
            $data['mode_paiement'] ?? null,
            $data['reference_paiement'] ?? null,
            $data['notes'] ?? null,
            $data['created_by'] ?? null
        ]) ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Obtenir les paiements d'un utilisateur
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.full_name as created_by_name
            FROM user_payments p
            LEFT JOIN users u ON p.created_by = u.id
            WHERE p.user_id = ?
            ORDER BY p.periode DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Marquer un paiement comme payé
     */
    public function markAsPaid($id, $datePaiement, $modePaiement = null, $reference = null) {
        $stmt = $this->db->prepare("
            UPDATE user_payments 
            SET statut = 'paye', date_paiement = ?, mode_paiement = ?, reference_paiement = ?
            WHERE id = ?
        ");
        return $stmt->execute([$datePaiement, $modePaiement, $reference, $id]);
    }
    
    /**
     * Générer les paiements mensuels pour un volontaire
     */
    public function generateMonthlyPayments($userId, $profileId, $montantMensuel, $dateDebut, $dateFin) {
        $payments = [];
        $start = new DateTime($dateDebut);
        $end = new DateTime($dateFin);
        
        while ($start <= $end) {
            $periode = $start->format('Y-m-01');
            $payments[] = [
                'user_id' => $userId,
                'profile_id' => $profileId,
                'montant' => $montantMensuel,
                'periode' => $periode,
                'type_paiement' => 'mensuel',
                'statut' => 'en_attente'
            ];
            $start->modify('+1 month');
        }
        
        foreach ($payments as $payment) {
            try {
                $this->create($payment);
            } catch (Exception $e) {
                // Ignorer les doublons
            }
        }
        
        return count($payments);
    }
}

