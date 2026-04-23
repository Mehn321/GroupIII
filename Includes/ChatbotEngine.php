<?php
/**
 * Chatbot Engine
 * Main logic for handling user queries and generating responses
 */

class ChatbotEngine {
    private $knowledge_base;
    private $semantic_search;
    
    public function __construct() {
        $this->knowledge_base = new KnowledgeBase();
        $this->semantic_search = new SemanticSearch();
    }
    
    /**
     * Process user query and generate response
     * Returns array with response data and metadata
     */
    public function processQuery($userQuery) {
        try {
            $response = [
                'success' => true,
                'user_query' => $userQuery,
                'source' => null,
                'answer' => null,
                'matches' => [],
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            // Step 1: Search database for matching questions
            $matches = $this->semantic_search->findSimilarQuestions($userQuery);
            $response['matches'] = $matches;
            
            if (!empty($matches)) {
                // Step 2: Return best database match
                $best_match = $matches[0];
                $response['source'] = 'knowledge_base';
                $response['answer'] = $best_match['answer'];
                $response['match_score'] = $best_match['similarity_score'];
                $response['matched_question'] = $best_match['question'];
                $response['category'] = $best_match['category'];
                
                // Log successful match
                $this->logQuery($userQuery, 'knowledge_base', $best_match['id'], $best_match['similarity_score']);
                
            } else {
                // Step 3: No good match found - generate fallback response
                $response['source'] = 'fallback';
                $response['answer'] = $this->generateFallbackResponse($userQuery);
                
                // Log fallback response
                $this->logQuery($userQuery, 'fallback', null, 0);
            }
            
            return $response;
            
        } catch (Exception $e) {
            error_log("Error in processQuery: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while processing your query: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Generate fallback response using Gemini AI API
     * Falls back to pattern matching if API fails
     */
    private function generateFallbackResponse($query) {
        // Try to use Gemini API first
        if (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)) {
            $ai_response = $this->callGeminiAPI($query);
            if ($ai_response) {
                return $ai_response;
            }
        }
        
        // Fallback to pattern matching if API fails
        return $this->generatePatternBasedResponse($query);
    }
    
    /**
     * Call Gemini API for AI-generated responses
     */
    private function callGeminiAPI($query) {
        try {
            // Check if curl is available
            if (!function_exists('curl_init')) {
                error_log("cURL is not available on this server");
                return null;
            }
            
            $url = GEMINI_API_BASE . '/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;
            
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "You are a helpful customer support assistant for an online store. Answer this customer question concisely and helpfully in 2-3 sentences: " . $query
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 500
                ]
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            error_log("Gemini API Response Code: " . $http_code);
            error_log("Gemini API Response: " . substr($response, 0, 500));
            
            if ($curl_error) {
                error_log("Gemini cURL Error: " . $curl_error);
                return null;
            }
            
            if ($http_code === 200 && $response) {
                $data = json_decode($response, true);
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $answer = $data['candidates'][0]['content']['parts'][0]['text'];
                    error_log("Gemini API Success: " . substr($answer, 0, 100));
                    return $answer;
                } else {
                    error_log("Gemini API: No text in response. Full response: " . json_encode($data));
                    return null;
                }
            } else {
                error_log("Gemini API Error - Status: " . $http_code . " Response: " . $response);
                return null;
            }
            
        } catch (Exception $e) {
            error_log("Gemini API Exception: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate response based on pattern matching (fallback for API failure)
     */
    private function generatePatternBasedResponse($query) {
        $query_lower = strtolower($query);
        
        // Simple pattern matching for common queries
        if (preg_match('/shipping|deliver|arrival|track/i', $query_lower)) {
            return "I couldn't find a specific answer in my knowledge base about shipping. Please contact our customer support team for accurate shipping information.";
        }
        
        if (preg_match('/refund|return|exchange/i', $query_lower)) {
            return "For return and refund inquiries, please contact our support team. They can provide detailed information based on your specific purchase.";
        }
        
        if (preg_match('/payment|card|billing|charge/i', $query_lower)) {
            return "Regarding payment issues, please contact our support team to ensure your account security.";
        }
        
        if (preg_match('/available|stock|in stock|sold out/i', $query_lower)) {
            return "For product availability information, please check our website or contact support.";
        }
        
        // Default fallback response
        return "I don't have a specific answer to that question in my knowledge base. Our support team would be happy to help. Please contact us directly for assistance.";
    }
    
    /**
     * Log all queries for analytics and improvement
     */
    private function logQuery($query, $source, $matched_question_id = null, $similarity_score = 0) {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare(
                "INSERT INTO query_logs (query_text, source, matched_question_id, similarity_score, created_at) 
                 VALUES (?, ?, ?, ?, NOW())"
            );
            
            $stmt->bind_param("ssii", $query, $source, $matched_question_id, $similarity_score);
            $stmt->execute();
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Error logging query: " . $e->getMessage());
        }
    }
    
    /**
     * Get conversation statistics
     */
    public function getStatistics() {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Total queries
            $result = $db->query("SELECT COUNT(*) as total FROM query_logs");
            $total_queries = $result->fetch_assoc()['total'];
            
            // Queries by source
            $result = $db->query(
                "SELECT source, COUNT(*) as count FROM query_logs GROUP BY source"
            );
            $by_source = [];
            while ($row = $result->fetch_assoc()) {
                $by_source[$row['source']] = $row['count'];
            }
            
            // Average similarity score
            $result = $db->query(
                "SELECT AVG(similarity_score) as avg_score FROM query_logs WHERE similarity_score > 0"
            );
            $avg_score = $result->fetch_assoc()['avg_score'];
            
            // Total questions in KB
            $result = $db->query("SELECT COUNT(*) as total FROM questions");
            $total_questions = $result->fetch_assoc()['total'];
            
            return [
                'total_queries' => $total_queries,
                'total_questions_in_kb' => $total_questions,
                'queries_by_source' => $by_source,
                'average_match_score' => round($avg_score, 3)
            ];
            
        } catch (Exception $e) {
            error_log("Error in getStatistics: " . $e->getMessage());
            return [];
        }
    }
}
?>
