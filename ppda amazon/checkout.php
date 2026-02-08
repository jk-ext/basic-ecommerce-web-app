<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    die("You must be logged in to checkout");
}

$email = $_SESSION['email'];
$conn->query("DELETE FROM orders WHERE email='$email'");
header("Location: checkout.html");
exit();
?>
