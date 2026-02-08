<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    die("You must be logged in to perform cart operations");
}

// Add item to cart
if (isset($_POST['add_to_cart'])) {
    $email = $_SESSION['email'];
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $product_price = $conn->real_escape_string($_POST['product_price']);
    $quantity = $conn->real_escape_string($_POST['quantity']);

    // Check if item already exists in orders
    $check = $conn->prepare("SELECT quantity FROM orders WHERE email=? AND product_name=?");
    $check->bind_param("ss", $email, $product_name);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update existing order item
        $existing = $result->fetch_assoc();
        $new_quantity = $existing['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE orders SET quantity=? WHERE email=? AND product_name=?");
        $update->bind_param("iss", $new_quantity, $email, $product_name);
        $update->execute();
        $update->close();
    } else {
        // Add new order item
        $stmt = $conn->prepare("INSERT INTO orders (email, product_name, product_price, quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $email, $product_name, $product_price, $quantity);
        $stmt->execute();
        $stmt->close();
    }
    $check->close();
}

$conn->close();
?>
