<?php
    require_once('functions.php');
    session_start();
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $host = 'localhost';
    $dbname = 'fursa';
    $user = 'root';
    $password = '';
    
    
    
    // PDO connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // echo "Connected successfully";
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    
    
    $apptitle="Furse Energy";
    $backButton = '<button onclick="history.back()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> &nbsp; Back
                  </button>';
    global $dbConn;
    $dbConn = new DatabaseHandler($pdo);
?>