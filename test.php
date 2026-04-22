<?php
/**
 * Quick Test Page - Test all components
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chatbot Test</title>
    <style>
        body { font-family: Arial; margin: 30px; background: #f5f5f5; }
        h1 { color: #333; }
        .test-section { background: white; padding: 20px; margin: 15px 0; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .pass { color: #27ae60; font-weight: bold; }
        .fail { color: #e74c3c; font-weight: bold; }
        .info { color: #3498db; }
        button { background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        button:hover { background: #764ba2; }
        #testOutput { margin-top: 10px; padding: 10px; background: #ecf0f1; border-radius: 5px; font-family: monospace; }
        .step { margin: 10px 0; padding: 10px; background: #ecf0f1; border-left: 4px solid #667eea; }
    </style>
</head>
<body>

<h1>🔧 Chatbot System Test</h1>

<div class="test-section">
    <h2>Step 1: Environment Check</h2>
    <p>PHP Version: <span class="info"><?php echo phpversion(); ?></span></p>
    <p>MySQLi Extension: <span class="<?php echo extension_loaded('mysqli') ? 'pass' : 'fail'; ?>">
        <?php echo extension_loaded('mysqli') ? '✓ Available' : '✗ NOT Available'; ?>
    </span></p>
    <p>Current URL: <span class="info"><?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></span></p>
</div>

<div class="test-section">
    <h2>Step 2: Configuration Check</h2>
    <?php
        require_once 'config.php';
        
        echo "<p>Database Host: <span class='info'>" . DB_HOST . "</span></p>";
        echo "<p>Database Name: <span class='info'>" . DB_NAME . "</span></p>";
        echo "<p>Database User: <span class='info'>" . DB_USER . "</span></p>";
    ?>
</div>

<div class="test-section">
    <h2>Step 3: MySQL Connection Test</h2>
    <button onclick="testMySQLConnection()">Test MySQL Connection</button>
    <div id="mysqlResult"></div>
</div>

<div class="test-section">
    <h2>Step 4: Database Tables Check</h2>
    <button onclick="testDatabaseTables()">Check Tables</button>
    <div id="tablesResult"></div>
</div>

<div class="test-section">
    <h2>Step 5: API Test</h2>
    <button onclick="testAPI()">Test API Endpoint</button>
    <div id="apiResult"></div>
</div>

<div class="test-section">
    <h2>Step 6: Setup Instructions</h2>
    <div class="step">
        <strong>If everything above shows errors, run these in order:</strong>
        <ol>
            <li><a href="setup_db.php" target="_blank" style="color: #667eea;"><strong>1. Setup Database</strong></a> - Creates database and tables</li>
            <li><a href="insert_sample_data.php" target="_blank" style="color: #667eea;"><strong>2. Load Sample Data</strong></a> - Adds Q&A pairs</li>
            <li><a href="index.html" target="_blank" style="color: #667eea;"><strong>3. Start Chatting</strong></a> - Open the chatbot</li>
        </ol>
    </div>
</div>

<script>
    function testMySQLConnection() {
        const resultDiv = document.getElementById('mysqlResult');
        resultDiv.innerHTML = 'Testing...';
        
        fetch('test_mysql.php')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<p class="pass">✓ MySQL Connected!</p><p>Database: ' + data.database + '</p>';
                } else {
                    resultDiv.innerHTML = '<p class="fail">✗ Connection Failed</p><p>' + data.error + '</p><p style="color: orange;">Make sure MySQL is running in XAMPP</p>';
                }
            })
            .catch(e => {
                resultDiv.innerHTML = '<p class="fail">✗ Error: ' + e.message + '</p>';
            });
    }
    
    function testDatabaseTables() {
        const resultDiv = document.getElementById('tablesResult');
        resultDiv.innerHTML = 'Testing...';
        
        fetch('test_tables.php')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = '<p class="pass">✓ All Tables Found!</p><p>' + data.message + '</p>';
                } else {
                    resultDiv.innerHTML = '<p class="fail">✗ Tables Not Found</p><p>' + data.error + '</p><p style="color: orange;">Click "Setup Database" link above</p>';
                }
            })
            .catch(e => {
                resultDiv.innerHTML = '<p class="fail">✗ Error: ' + e.message + '</p>';
            });
    }
    
    function testAPI() {
        const resultDiv = document.getElementById('apiResult');
        resultDiv.innerHTML = 'Testing...';
        
        fetch('api/chatbot.php?action=query', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: 'test' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = '<p class="pass">✓ API is Working!</p><p>Response: ' + data.answer.substring(0, 100) + '...</p>';
            } else {
                resultDiv.innerHTML = '<p class="fail">✗ API Error</p><p>' + (data.error || 'Unknown error') + '</p>';
            }
        })
        .catch(e => {
            resultDiv.innerHTML = '<p class="fail">✗ API Connection Failed</p><p>' + e.message + '</p>';
        });
    }
</script>

</body>
</html>
