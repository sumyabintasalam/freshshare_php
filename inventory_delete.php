<?php
// inventory_delete.php
// Deleting the item also removes any Shared_Listings / Claims rows tied to
// it, via the ON DELETE CASCADE foreign keys defined in schema.sql.
require_once __DIR__ . '/includes/auth.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int) ($_POST['item_id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM Inventory_Items WHERE item_id = ? AND user_id = ?");
    $stmt->execute([$item_id, $user['user_id']]);
    setFlash('success', 'Item deleted.');
}

header('Location: dashboard.php');
exit;
