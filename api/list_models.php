<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

try {
    if (!function_exists('curl_init')) {
        throw new Exception('cURL is not available on this server');
    }
    
    // Test 1: List available models
    echo "Testing available models...\n\n";
    
    $list_url = GEMINI_API_BASE . '/models?key=' . GEMINI_API_KEY;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $list_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $data = json_decode($response, true);
        if (isset($data['models'])) {
            echo "Available Models:\n";
            foreach ($data['models'] as $model) {
                echo "- " . $model['name'] . "\n";
            }
        }
    } else {
        echo "Could not list models (HTTP " . $http_code . ")\n";
        echo "Response: " . substr($response, 0, 300) . "\n\n";
    }
    
    // Test 2: Try different model names
    echo "\n\nTesting different model names:\n";
    
    $models_to_test = [
        'gemini-1.5-flash',
        'gemini-1.5-pro',
        'gemini-pro',
        'gemini-1.0-pro'
    ];
    
    foreach ($models_to_test as $model) {
        $test_url = GEMINI_API_BASE . '/models/' . $model . ':generateContent?key=' . GEMINI_API_KEY;
        
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'test']
                    ]
                ]
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $test_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $status = ($http_code === 200) ? '✓ OK' : '✗ HTTP ' . $http_code;
        echo $model . ": " . $status . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
