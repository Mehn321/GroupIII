<?php
/**
 * Admin API Endpoint
 * Handles CRUD operations for questions and answers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Autoload classes
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/KnowledgeBase.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple authentication check (in production, use proper auth)
function checkAdminAccess() {
    // TODO: Implement proper authentication
    // For now, anyone can access. Add token or session validation here
    return true;
}

try {
    if (!checkAdminAccess()) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'Unauthorized access'
        ]);
        exit;
    }
    
    $request_method = $_SERVER['REQUEST_METHOD'];
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    $kb = new KnowledgeBase();
    
    if ($request_method === 'GET' && $action === 'all') {
        // Get all Q&A pairs
        $qa_pairs = $kb->getAllQA();
        echo json_encode([
            'success' => true,
            'data' => $qa_pairs,
            'count' => count($qa_pairs)
        ]);
        
    } elseif ($request_method === 'GET' && $action === 'get') {
        // Get specific Q&A
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID parameter required']);
            exit;
        }
        
        $qa = $kb->getQA($_GET['id']);
        if ($qa) {
            echo json_encode(['success' => true, 'data' => $qa]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Q&A pair not found']);
        }
        
    } elseif ($request_method === 'GET' && $action === 'categories') {
        // Get all categories
        $categories = $kb->getCategories();
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
        
    } elseif ($request_method === 'POST' && $action === 'add') {
        // Add new Q&A pair
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['question']) || !isset($data['answer']) || !isset($data['category_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Missing required fields: question, answer, category_id'
            ]);
            exit;
        }
        
        $question_id = $kb->addQA(
            $data['question'],
            $data['answer'],
            $data['category_id'],
            isset($data['keywords']) ? $data['keywords'] : ''
        );
        
        if ($question_id) {
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Q&A pair created successfully',
                'id' => $question_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create Q&A pair']);
        }
        
    } elseif ($request_method === 'PUT' && $action === 'update') {
        // Update Q&A pair
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['question']) || !isset($data['answer']) || !isset($data['category_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Missing required fields: id, question, answer, category_id'
            ]);
            exit;
        }
        
        $success = $kb->updateQA(
            $data['id'],
            $data['question'],
            $data['answer'],
            $data['category_id'],
            isset($data['keywords']) ? $data['keywords'] : ''
        );
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Q&A pair updated successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update Q&A pair']);
        }
        
    } elseif ($request_method === 'DELETE' && $action === 'delete') {
        // Delete Q&A pair
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID parameter required']);
            exit;
        }
        
        $success = $kb->deleteQA($_GET['id']);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Q&A pair deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete Q&A pair']);
        }
        
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid request. Available actions: all, get, categories, add, update, delete'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
