<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$query = isset($_POST) ? file_get_contents('php://input') : '';
$data = json_decode($query, true);
$user_query = isset($data['query']) ? $data['query'] : 'test';

try {
    if (!function_exists('curl_init')) {
        throw new Exception('cURL is not available on this server');
    }
    
    $url = GEMINI_API_BASE . '/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY;
    
    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => "You are a helpful customer support assistant. Answer this question concisely: " . $user_query
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
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($curl_error) {
        throw new Exception('cURL Error: ' . $curl_error);
    }
    
    if ($http_code !== 200) {
        throw new Exception('HTTP ' . $http_code . ': ' . substr($response, 0, 200));
    }
    
    $api_data = json_decode($response, true);
    
    if (!isset($api_data['candidates'][0]['content']['parts'][0]['text'])) {
        throw new Exception('Invalid API response format. Response: ' . json_encode($api_data));
    }
    
    $answer = $api_data['candidates'][0]['content']['parts'][0]['text'];
    
    echo json_encode([
        'success' => true,
        'response' => $answer
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'details' => 'Make sure your Gemini API key is valid and you have internet connection.'
    ]);
}
?>
