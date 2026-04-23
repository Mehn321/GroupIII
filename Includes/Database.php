<?php
/**
 * Database Connection Class
 * Handles all database operations with singleton pattern
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->connection->connect_error) {
                throw new Exception("MySQL connection failed: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            // Send JSON error response instead of dying
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'debug_info' => 'Database connection failed. Check config.php and ensure MySQL is running.'
            ]);
            exit();
        }
    }
    
    /**
     * Get singleton instance of database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get mysqli connection object
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a query
     */
    public function query($sql) {
        $result = $this->connection->query($sql);
        
        if (!$result) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        
        return $result;
    }
    
    /**
     * Execute a prepared statement
     */
    public function prepare($sql) {
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        return $stmt;
    }
    
    /**
     * Get last insert ID
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * Get affected rows
     */
    public function getAffectedRows() {
        return $this->connection->affected_rows;
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Escape string for safe queries
     */
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }
}
?>
