<?php
// inventory_share.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/status.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int) ($_POST['item_id'] ?? 0);
    $pickup_note = trim($_POST['pickup_note'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM Inventory_Items WHERE item_id = ? AND user_id = ?");
    $stmt->execute([$item_id, $user['user_id']]);
    $item = $stmt->fetch();

    if (!$item) {
        header('Location: dashboard.php');
        exit;
    }

    $status = computeStatus($item['expiration_date']);
    if ($status['label'] === 'Expired') {
        setFlash('error', 'Expired items cannot be shared.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO Shared_Listings (item_id, pickup_note) VALUES (?, ?)");
        $stmt->execute([$item_id, $pickup_note]);
        setFlash('success', 'Item shared with the community.');
    }
}

header('Location: dashboard.php');
exit;
