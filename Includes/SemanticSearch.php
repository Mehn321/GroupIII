<?php
/**
 * Semantic Search and Similarity Calculator
 * Handles similarity matching using multiple algorithms
 */

class SemanticSearch {
    private $db;
    private $knowledge_base;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->knowledge_base = new KnowledgeBase();
    }
    
    /**
     * Calculate similarity between two strings using Levenshtein distance
     * Returns a score between 0 and 1
     */
    private function levenshteinSimilarity($str1, $str2) {
        $strlen1 = strlen($str1);
        $strlen2 = strlen($str2);
        $maxlen = max($strlen1, $strlen2);
        
        if ($maxlen === 0) {
            return 1.0;
        }
        
        $distance = levenshtein($str1, $str2);
        return 1 - ($distance / $maxlen);
    }
    
    /**
     * Calculate cosine similarity using word tokenization
     * More semantically aware than simple string matching
     */
    private function cosineSimilarity($text1, $text2) {
        // Tokenize and normalize
        $words1 = $this->tokenize($text1);
        $words2 = $this->tokenize($text2);
        
        if (empty($words1) || empty($words2)) {
            return 0;
        }
        
        // Calculate term frequency vectors
        $vector1 = array_count_values($words1);
        $vector2 = array_count_values($words2);
        
        // Calculate dot product
        $dotProduct = 0;
        foreach ($vector1 as $word => $count1) {
            if (isset($vector2[$word])) {
                $dotProduct += $count1 * $vector2[$word];
            }
        }
        
        // Calculate magnitudes
        $magnitude1 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector1)));
        $magnitude2 = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $vector2)));
        
        if ($magnitude1 === 0 || $magnitude2 === 0) {
            return 0;
        }
        
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
    
    /**
     * Tokenize text into words
     */
    private function tokenize($text) {
        // Convert to lowercase and remove special characters
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        
        // Split into words and remove common stop words
        $words = array_filter(preg_split('/\s+/', $text));
        $stopwords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'is', 'are', 'was', 'were', 'be', 'have', 'has', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'can', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they'];
        
        return array_filter($words, function($word) use ($stopwords) {
            return !in_array($word, $stopwords) && strlen($word) > 2;
        });
    }
    
    /**
     * Calculate Jaccard similarity (set-based similarity)
     * Measures overlap between word sets
     */
    private function jaccardSimilarity($text1, $text2) {
        $set1 = array_unique($this->tokenize($text1));
        $set2 = array_unique($this->tokenize($text2));
        
        if (empty($set1) && empty($set2)) {
            return 1.0;
        }
        
        if (empty($set1) || empty($set2)) {
            return 0;
        }
        
        $intersection = count(array_intersect($set1, $set2));
        $union = count($this->array_union($set1, $set2));
        
        return $union === 0 ? 0 : $intersection / $union;
    }
    
    /**
     * Helper function for array union
     */
    private function array_union($a, $b) {
        return array_unique(array_merge($a, $b));
    }
    
    /**
     * Calculate combined similarity score
     * Uses weighted average of multiple algorithms
     */
    public function calculateSimilarity($userQuery, $storedQuestion) {
        // Normalize queries
        $userQuery = trim($userQuery);
        $storedQuestion = trim($storedQuestion);
        
        // Calculate individual similarities
        $leven = $this->levenshteinSimilarity($userQuery, $storedQuestion);
        $cosine = $this->cosineSimilarity($userQuery, $storedQuestion);
        $jaccard = $this->jaccardSimilarity($userQuery, $storedQuestion);
        
        // Weighted average (cosine gets more weight for semantic understanding)
        $similarity = (
            $leven * 0.2 +
            $cosine * 0.5 +
            $jaccard * 0.3
        );
        
        return min(1, max(0, $similarity));
    }
    
    /**
     * Search database for semantically similar questions
     * Returns best matches sorted by similarity score
     */
    public function findSimilarQuestions($userQuery, $threshold = SIMILARITY_THRESHOLD, $limit = MAX_RESULTS) {
        try {
            // Get all questions from database
            $all_questions = $this->knowledge_base->getAllQA();
            
            if (empty($all_questions)) {
                return [];
            }
            
            // Calculate similarity for each question
            $results = [];
            foreach ($all_questions as $qa) {
                $similarity = $this->calculateSimilarity($userQuery, $qa['question_text']);
                
                if ($similarity >= $threshold) {
                    $results[] = [
                        'id' => $qa['id'],
                        'question' => $qa['question_text'],
                        'answer' => $qa['answer_text'],
                        'category' => $qa['category'],
                        'category_id' => $qa['category_id'],
                        'keywords' => $qa['keywords'],
                        'similarity_score' => round($similarity, 3),
                        'created_at' => $qa['created_at'],
                        'updated_at' => $qa['updated_at']
                    ];
                }
            }
            
            // Sort by similarity score (highest first)
            usort($results, function($a, $b) {
                return $b['similarity_score'] <=> $a['similarity_score'];
            });
            
            // Return top results
            return array_slice($results, 0, $limit);
            
        } catch (Exception $e) {
            error_log("Error in findSimilarQuestions: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find best single match from database
     */
    public function findBestMatch($userQuery, $threshold = SIMILARITY_THRESHOLD) {
        $results = $this->findSimilarQuestions($userQuery, $threshold, 1);
        return !empty($results) ? $results[0] : null;
    }
}
?>
