<?php
/**
 * Database Schema Setup
 * Run this file once to create all necessary tables
 */

// Database credentials
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'chatbot_db';

// Create connection without selecting database first
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if (!$conn->query($sql)) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($db_name);

// Set charset
$conn->set_charset("utf8mb4");

// Create Categories table
$categories_sql = "
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Create Questions table
$questions_sql = "
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    category_id INT,
    keywords VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_keywords (keywords)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Create Answers table
$answers_sql = "
CREATE TABLE IF NOT EXISTS answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer_text LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Create Query Logs table for analytics
$query_logs_sql = "
CREATE TABLE IF NOT EXISTS query_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    query_text TEXT NOT NULL,
    source ENUM('knowledge_base', 'fallback') NOT NULL,
    matched_question_id INT,
    similarity_score DECIMAL(3, 3),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_source (source),
    INDEX idx_created (created_at),
    FOREIGN KEY (matched_question_id) REFERENCES questions(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Execute table creation
$tables = [
    'categories' => $categories_sql,
    'questions' => $questions_sql,
    'answers' => $answers_sql,
    'query_logs' => $query_logs_sql
];

$errors = [];
foreach ($tables as $table_name => $sql) {
    if ($conn->query($sql)) {
        echo "✓ Table '$table_name' created successfully.<br>";
    } else {
        $errors[] = "Error creating '$table_name': " . $conn->error;
        echo "✗ Error creating '$table_name': " . $conn->error . "<br>";
    }
}

// Insert sample categories
$sample_categories = [
    ['Orders & Tracking', 'Questions about order status and package tracking'],
    ['Shipping & Delivery', 'Questions about shipping options and delivery timeframes'],
    ['Returns & Refunds', 'Questions about return policies and refund processes'],
    ['Payment & Billing', 'Questions about payment methods and billing issues'],
    ['Product Availability', 'Questions about product stock and availability'],
    ['Account & Login', 'Questions about account management and login issues'],
    ['Warranty & Support', 'Questions about warranty and technical support']
];

echo "<br><strong>Inserting Sample Categories:</strong><br>";

$insert_category_sql = "INSERT IGNORE INTO categories (name, description) VALUES (?, ?)";
$stmt = $conn->prepare($insert_category_sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

foreach ($sample_categories as $category) {
    $stmt->bind_param("ss", $category[0], $category[1]);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "✓ Category '{$category[0]}' inserted.<br>";
        } else {
            echo "→ Category '{$category[0]}' already exists (skipped).<br>";
        }
    } else {
        echo "✗ Error inserting category: " . $stmt->error . "<br>";
    }
}

$stmt->close();

// Display summary
echo "<br><hr>";
if (empty($errors)) {
    echo "<strong style='color: green;'>✓ Database schema setup completed successfully!</strong>";
} else {
    echo "<strong style='color: red;'>✗ Setup completed with errors:</strong><br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
}

$conn->close();
?>
