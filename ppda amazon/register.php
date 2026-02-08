<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get and sanitize input
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        $rePassword = $_POST['rePassword'];

        // Validate passwords match
        if ($password !== $rePassword) {
            throw new Exception("Passwords do not match");
        }

        // Start transaction
        $conn->begin_transaction();

        try {
            // Prepare and execute SQL for 'register' table
            $stmt = $conn->prepare("INSERT INTO register (username, email, password) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sss", $username, $email, $password);
            
            // Store username in session
            $_SESSION['username'] = $username;
            $stmt->execute();
            $stmt->close();

            // Prepare and execute SQL for 'login' table
            $stmt = $conn->prepare("INSERT INTO login (email, password) VALUES (?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();

            $conn->commit();
            header("Location: registration_success.html");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            die("Error: " . $e->getMessage());
        }
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
}
?>
