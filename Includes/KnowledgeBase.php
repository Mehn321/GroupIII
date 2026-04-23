<?php
/**
 * Knowledge Base Manager
 * Handles all knowledge base operations (CRUD for Q&A pairs)
 */

class KnowledgeBase {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Add a new question-answer pair
     */
    public function addQA($question, $answer, $category_id, $keywords = '') {
        try {
            // Check if question already exists
            $check_stmt = $this->db->prepare(
                "SELECT id FROM questions WHERE question_text = ? AND category_id = ?"
            );
            $check_stmt->bind_param("si", $question, $category_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Question already exists, skip
                $check_stmt->close();
                return false;
            }
            $check_stmt->close();
            
            $stmt = $this->db->prepare(
                "INSERT INTO questions (question_text, category_id, keywords, created_at, updated_at) 
                 VALUES (?, ?, ?, NOW(), NOW())"
            );
            
            $stmt->bind_param("sis", $question, $category_id, $keywords);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert question: " . $stmt->error);
            }
            
            $question_id = $stmt->insert_id;
            $stmt->close();
            
            // Insert answer
            $stmt = $this->db->prepare(
                "INSERT INTO answers (question_id, answer_text, created_at, updated_at) 
                 VALUES (?, ?, NOW(), NOW())"
            );
            
            $stmt->bind_param("is", $question_id, $answer);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert answer: " . $stmt->error);
            }
            
            $stmt->close();
            return $question_id;
            
        } catch (Exception $e) {
            error_log("Error in addQA: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update question and answer
     */
    public function updateQA($question_id, $question, $answer, $category_id, $keywords = '') {
        try {
            $stmt = $this->db->prepare(
                "UPDATE questions SET question_text = ?, category_id = ?, keywords = ?, updated_at = NOW() 
                 WHERE id = ?"
            );
            
            $stmt->bind_param("sisi", $question, $category_id, $keywords, $question_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update question: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Update answer
            $stmt = $this->db->prepare(
                "UPDATE answers SET answer_text = ?, updated_at = NOW() 
                 WHERE question_id = ?"
            );
            
            $stmt->bind_param("si", $answer, $question_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update answer: " . $stmt->error);
            }
            
            $stmt->close();
            return true;
            
        } catch (Exception $e) {
            error_log("Error in updateQA: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete question-answer pair
     */
    public function deleteQA($question_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM answers WHERE question_id = ?");
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $stmt->close();
            
            $stmt = $this->db->prepare("DELETE FROM questions WHERE id = ?");
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $stmt->close();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error in deleteQA: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all questions with answers and categories
     */
    public function getAllQA() {
        try {
            $result = $this->db->query(
                "SELECT q.id, q.question_text, a.answer_text, c.name as category, 
                        c.id as category_id, q.keywords, q.created_at, q.updated_at
                 FROM questions q
                 LEFT JOIN answers a ON q.id = a.question_id
                 LEFT JOIN categories c ON q.category_id = c.id
                 ORDER BY q.created_at DESC"
            );
            
            $qa_pairs = [];
            while ($row = $result->fetch_assoc()) {
                $qa_pairs[] = $row;
            }
            
            return $qa_pairs;
            
        } catch (Exception $e) {
            error_log("Error in getAllQA: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get specific Q&A pair
     */
    public function getQA($question_id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT q.id, q.question_text, a.answer_text, c.name as category, 
                        c.id as category_id, q.keywords, q.created_at, q.updated_at
                 FROM questions q
                 LEFT JOIN answers a ON q.id = a.question_id
                 LEFT JOIN categories c ON q.category_id = c.id
                 WHERE q.id = ?"
            );
            
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $qa = $result->fetch_assoc();
            $stmt->close();
            
            return $qa;
            
        } catch (Exception $e) {
            error_log("Error in getQA: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search questions by category
     */
    public function searchByCategory($category_id) {
        try {
            $stmt = $this->db->prepare(
                "SELECT q.id, q.question_text, a.answer_text, c.name as category, 
                        c.id as category_id, q.keywords, q.created_at, q.updated_at
                 FROM questions q
                 LEFT JOIN answers a ON q.id = a.question_id
                 LEFT JOIN categories c ON q.category_id = c.id
                 WHERE q.category_id = ?
                 ORDER BY q.created_at DESC"
            );
            
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $qa_pairs = [];
            while ($row = $result->fetch_assoc()) {
                $qa_pairs[] = $row;
            }
            
            $stmt->close();
            return $qa_pairs;
            
        } catch (Exception $e) {
            error_log("Error in searchByCategory: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Full-text search in questions
     */
    public function searchQuestions($query) {
        try {
            $search_term = '%' . $this->db->real_escape_string($query) . '%';
            
            $stmt = $this->db->prepare(
                "SELECT q.id, q.question_text, a.answer_text, c.name as category, 
                        c.id as category_id, q.keywords, q.created_at, q.updated_at
                 FROM questions q
                 LEFT JOIN answers a ON q.id = a.question_id
                 LEFT JOIN categories c ON q.category_id = c.id
                 WHERE q.question_text LIKE ? OR q.keywords LIKE ? OR a.answer_text LIKE ?
                 ORDER BY q.created_at DESC
                 LIMIT 10"
            );
            
            $stmt->bind_param("sss", $search_term, $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $qa_pairs = [];
            while ($row = $result->fetch_assoc()) {
                $qa_pairs[] = $row;
            }
            
            $stmt->close();
            return $qa_pairs;
            
        } catch (Exception $e) {
            error_log("Error in searchQuestions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get categories
     */
    public function getCategories() {
        try {
            $result = $this->db->query(
                "SELECT id, name, description FROM categories ORDER BY name"
            );
            
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            
            return $categories;
            
        } catch (Exception $e) {
            error_log("Error in getCategories: " . $e->getMessage());
            return [];
        }
    }
}
?>
