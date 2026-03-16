<?php
/**
 * Classe Document - Gestion des documents
 * RAMP-BENIN
 */

class Document {
    private $db;
    private $uploadsDir = '/uploads/documents/';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtenir un document par ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT d.*, u.full_name as uploaded_by_name
            FROM documents d
            LEFT JOIN users u ON d.uploaded_by = u.id
            WHERE d.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir tous les documents
     * @param string|null $category Filtrer par catégorie
     * @param string|null $level Filtrer par niveau (departement, regional, sous_regional)
     * @return array
     */
    public function getAll($category = null, $level = null) {
        try {
            $sql = "
                SELECT d.*, u.full_name as uploaded_by_name
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE 1=1
            ";
            
            $params = [];
            
            if ($category) {
                $sql .= " AND d.category = ?";
                $params[] = $category;
            }
            
            if ($level) {
                $sql .= " AND d.level = ?";
                $params[] = $level;
            }
            
            $sql .= " ORDER BY d.created_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Si la table n'existe pas, retourner un tableau vide
            if (strpos($e->getMessage(), 'Table') !== false && strpos($e->getMessage(), 'doesn\'t exist') !== false) {
                return [];
            }
            throw $e;
        }
    }
    
    /**
     * Créer un document
     * @param array $data
     * @return int|false ID du nouveau document
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO documents (title, description, category, level, department, file_path, file_name, file_size, file_type, uploaded_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['category'],
            $data['level'],
            $data['department'] ?? null,
            $data['file_path'],
            $data['file_name'],
            $data['file_size'],
            $data['file_type'],
            $data['uploaded_by']
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Mettre à jour un document
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE documents 
            SET title = ?, description = ?, category = ?, level = ?, department = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['category'],
            $data['level'],
            $data['department'] ?? null,
            $id
        ]);
    }
    
    /**
     * Supprimer un document
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $document = $this->getById($id);
        if ($document) {
            // Supprimer le fichier physique
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $document['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Supprimer l'enregistrement
            $stmt = $this->db->prepare("DELETE FROM documents WHERE id = ?");
            return $stmt->execute([$id]);
        }
        return false;
    }
    
    /**
     * Obtenir les statistiques des documents
     * @return array
     */
    public function getStats() {
        $stats = [
            'total' => 0,
            'by_category' => [],
            'by_level' => [],
            'by_department' => []
        ];
        
        try {
            // Total
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM documents");
            $stats['total'] = $stmt->fetch()['count'];
            
            // Par catégorie
            $stmt = $this->db->query("
                SELECT category, COUNT(*) as count 
                FROM documents 
                GROUP BY category
            ");
            $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // Par niveau
            $stmt = $this->db->query("
                SELECT level, COUNT(*) as count 
                FROM documents 
                GROUP BY level
            ");
            $stats['by_level'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // Par département
            $stmt = $this->db->query("
                SELECT department, COUNT(*) as count 
                FROM documents 
                WHERE department IS NOT NULL
                GROUP BY department
            ");
            $stats['by_department'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            // Si la table n'existe pas, retourner les stats vides
            if (!(strpos($e->getMessage(), 'Table') !== false && strpos($e->getMessage(), 'doesn\'t exist') !== false)) {
                throw $e;
            }
        }
        
        return $stats;
    }
    
    /**
     * Obtenir les catégories uniques
     * @return array
     */
    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT category FROM documents WHERE category IS NOT NULL ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    /**
     * Obtenir les départements uniques
     * @return array
     */
    public function getDepartments() {
        $stmt = $this->db->query("SELECT DISTINCT department FROM documents WHERE department IS NOT NULL ORDER BY department");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
