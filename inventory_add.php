<?php
require_once __DIR__ . '/includes/auth.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name'] ?? '');
    $category = trim($_POST['category'] ?? 'Other');
    $quantity = $_POST['quantity'] ?? '';
    $unit = trim($_POST['unit'] ?? '');
    $expiration_date = $_POST['expiration_date'] ?? '';

    if ($item_name === '' || $quantity === '' || $unit === '' || $expiration_date === '') {
        setFlash('error', 'All fields are required.');
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO Inventory_Items (user_id, item_name, category, quantity, unit, expiration_date)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user['user_id'], $item_name, $category, $quantity, $unit, $expiration_date]);
        setFlash('success', 'Item added.');
    }
}

header('Location: dashboard.php');
exit;
