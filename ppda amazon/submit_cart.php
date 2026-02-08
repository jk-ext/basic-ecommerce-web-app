<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    die("You must be logged in to submit your cart");
}

$email = $_SESSION['email'];

// Get the cart data sent via POST as JSON
$cart_json = $_POST['cart_data'] ?? '';

if (empty($cart_json)) {
    die("No cart data received");
}

$cart_items = json_decode($cart_json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid cart data format");
}

foreach ($cart_items as $item) {
    $product_name = $conn->real_escape_string($item['product_name']);
    $product_price = floatval($item['product_price']);
    $quantity = intval($item['quantity']);

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

// Redirect to checkout confirmation page
header("Location: checkout.html");
exit();
?>
