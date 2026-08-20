<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/status.php';

$user = requireLogin();

$stmt = $pdo->prepare("
  SELECT l.listing_id, l.pickup_note, l.is_available,
         i.item_id, i.item_name, i.category, i.quantity, i.unit, i.expiration_date,
         u.name AS owner_name, u.area AS owner_area
  FROM Shared_Listings l
  JOIN Inventory_Items i ON l.item_id = i.item_id
  JOIN Users u ON i.user_id = u.user_id
  WHERE l.is_available = TRUE AND i.user_id != ?
  ORDER BY l.listed_at DESC
");
$stmt->execute([$user['user_id']]);
$rows = $stmt->fetchAll();

$title = "Community";
include __DIR__ . '/includes/header.php';
?>
<h1>Community Sharing</h1>
<p class="muted">Surplus items shared by others near you, listed most recent first.</p>
<?php if (empty($rows)): ?>
  <p class="muted">No items are currently shared. Check back later!</p>
<?php else: ?>
  <div class="listing-grid">
  <?php foreach ($rows as $row): $status = computeStatus($row['expiration_date']); ?>
    <div class="listing-card">
      <h3><?= escapeHtml($row['item_name']) ?> <span class="badge <?= $status['cssClass'] ?>"><?= escapeHtml($status['label']) ?></span></h3>
      <p class="muted"><?= escapeHtml($row['quantity']) ?> <?= escapeHtml($row['unit']) ?> &middot; <?= escapeHtml($row['category']) ?></p>
      <p>Expires: <?= escapeHtml($row['expiration_date']) ?></p>
      <p>Shared by: <?= escapeHtml($row['owner_name']) ?> (<?= escapeHtml($row['owner_area']) ?>)</p>
      <?php if (!empty($row['pickup_note'])): ?>
        <p class="pickup-note"><?= escapeHtml($row['pickup_note']) ?></p>
      <?php endif; ?>
      <form method="POST" action="listing_claim.php" onsubmit="return confirm('Claim this item? The owner will be notified.');">
        <input type="hidden" name="listing_id" value="<?= (int) $row['listing_id'] ?>">
        <button type="submit" class="btn btn-primary btn-small">Claim</button>
      </form>
    </div>
  <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
