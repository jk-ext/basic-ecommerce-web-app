<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Get user from register table
    $stmt = $conn->prepare("SELECT username, email, password FROM register WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if ($password === $user['password']) {
            // Authentication successful
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            header("Location: index.html");
            exit();
        } else {
            // Invalid password
            header("Location: login.html?error=Invalid email or password");
            exit();
        }
    } else {
        // User not found
        header("Location: login.html?error=Invalid email or password");
        exit();
    }
    
    $stmt->close();
    $conn->close();
}
?>
