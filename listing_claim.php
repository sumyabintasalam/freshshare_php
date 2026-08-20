<?php
require_once __DIR__ . '/includes/auth.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $listing_id = (int) ($_POST['listing_id'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT l.*, i.user_id AS owner_id
        FROM Shared_Listings l
        JOIN Inventory_Items i ON l.item_id = i.item_id
        WHERE l.listing_id = ?
    ");
    $stmt->execute([$listing_id]);
    $listing = $stmt->fetch();

    if (!$listing || !$listing['is_available']) {
        setFlash('error', 'That item is no longer available.');
    } elseif ((int) $listing['owner_id'] === (int) $user['user_id']) {
        setFlash('error', 'You cannot claim your own listing.');
    } else {
       
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO Claims (listing_id, claimed_by) VALUES (?, ?)");
            $stmt->execute([$listing_id, $user['user_id']]);

            $stmt = $pdo->prepare("UPDATE Shared_Listings SET is_available = FALSE WHERE listing_id = ?");
            $stmt->execute([$listing_id]);

            $pdo->commit();
            setFlash('success', 'Item claimed! Coordinate pickup using the note provided.');
        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash('error', 'Something went wrong. Please try again.');
        }
    }
}

header('Location: community.php');
exit;
