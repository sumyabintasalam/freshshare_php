<?php
require_once __DIR__ . '/includes/auth.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int) ($_POST['item_id'] ?? 0);
    $quantity = $_POST['quantity'] ?? null;

    // Ownership check: only update items that belong to the logged-in user.
    $stmt = $pdo->prepare("SELECT item_id FROM Inventory_Items WHERE item_id = ? AND user_id = ?");
    $stmt->execute([$item_id, $user['user_id']]);
    $owns = $stmt->fetch();

    if ($owns && is_numeric($quantity) && $quantity >= 0) {
        $stmt = $pdo->prepare("UPDATE Inventory_Items SET quantity = ? WHERE item_id = ?");
        $stmt->execute([$quantity, $item_id]);
        setFlash('success', 'Quantity updated.');
    } else {
        setFlash('error', 'Invalid quantity.');
    }
}

header('Location: dashboard.php');
exit;
