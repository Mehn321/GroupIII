<?php
/**
 * Chatbot API Endpoint
 * Handles user queries and returns JSON responses
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'PHP Error: ' . $errstr . ' in ' . basename($errfile) . ':' . $errline
    ]);
    exit;
});

// Autoload classes
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/KnowledgeBase.php';
require_once __DIR__ . '/../includes/SemanticSearch.php';
require_once __DIR__ . '/../includes/ChatbotEngine.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $request_method = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($request_method === 'POST' && $action === 'query') {
        // Process user query
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['query']) || empty(trim($data['query']))) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Query parameter is required and cannot be empty'
            ]);
            exit;
        }
        
        $user_query = trim($data['query']);
        $chatbot = new ChatbotEngine();
        $response = $chatbot->processQuery($user_query);
        
        http_response_code(200);
        echo json_encode($response);
        
    } elseif ($request_method === 'GET' && $action === 'stats') {
        // Get chatbot statistics
        $chatbot = new ChatbotEngine();
        $stats = $chatbot->getStatistics();
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
        
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid request. Use action=query (POST) or action=stats (GET)'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Fatal error: ' . $e->getMessage()
    ]);
}
?>
