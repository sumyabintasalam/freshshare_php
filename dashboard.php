<?php
// dashboard.php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/status.php';

$user = requireLogin();

$stmt = $pdo->prepare("SELECT * FROM Inventory_Items WHERE user_id = ? ORDER BY expiration_date ASC");
$stmt->execute([$user['user_id']]);
$items = $stmt->fetchAll();

$title = "My Inventory";
include __DIR__ . '/includes/header.php';
?>
<h1>My Inventory</h1>

<section class="form-card">
  <h2>Add a new item</h2>
  <form method="POST" action="inventory_add.php" class="grid-form">
    <label>Item name
      <input type="text" name="item_name" required placeholder="e.g., Milk">
    </label>
    <label>Category
      <select name="category">
        <option>Dairy</option>
        <option>Produce</option>
        <option>Grain</option>
        <option>Canned</option>
        <option>Meat</option>
        <option>Other</option>
      </select>
    </label>
    <label>Quantity
      <input type="number" step="0.01" min="0" name="quantity" required value="1">
    </label>
    <label>Unit
      <input type="text" name="unit" required placeholder="e.g., pcs, kg, litre">
    </label>
    <label>Expiration date
      <input type="date" name="expiration_date" required>
    </label>
    <button type="submit" class="btn btn-primary">Add Item</button>
  </form>
</section>

<section>
  <h2>Current items (<?= count($items) ?>)</h2>
  <?php if (empty($items)): ?>
    <p class="muted">No items yet — add your first item above.</p>
  <?php else: ?>
    <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Item</th><th>Category</th><th>Qty</th><th>Expires</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($items as $item):
        $status = computeStatus($item['expiration_date']);
        $dayLabel = $status['label'] === 'Expired'
          ? abs($status['days']) . ' day(s) ago'
          : $status['days'] . ' day(s) left';
      ?>
        <tr class="<?= $status['cssClass'] ?>">
          <td data-label="Item"><?= escapeHtml($item['item_name']) ?></td>
          <td data-label="Category"><?= escapeHtml($item['category']) ?></td>
          <td data-label="Qty"><?= escapeHtml($item['quantity']) ?> <?= escapeHtml($item['unit']) ?></td>
          <td data-label="Expires"><?= escapeHtml($item['expiration_date']) ?></td>
          <td data-label="Status">
            <span class="badge <?= $status['cssClass'] ?>"><?= escapeHtml($status['label']) ?></span>
            <small><?= escapeHtml($dayLabel) ?></small>
          </td>
          <td data-label="Actions" class="actions">
            <form method="POST" action="inventory_update.php" class="inline-form">
              <input type="hidden" name="item_id" value="<?= (int) $item['item_id'] ?>">
              <input type="number" step="0.01" min="0" name="quantity" value="<?= escapeHtml($item['quantity']) ?>" class="qty-input">
              <button type="submit" class="btn btn-small">Update</button>
            </form>
            <?php if ($status['label'] !== 'Expired'): ?>
              <form method="POST" action="inventory_share.php" class="inline-form">
                <input type="hidden" name="item_id" value="<?= (int) $item['item_id'] ?>">
                <input type="text" name="pickup_note" placeholder="Pickup note (e.g., porch, 6-8pm)" class="note-input">
                <button type="submit" class="btn btn-small btn-share">Share</button>
              </form>
            <?php else: ?>
              <span class="muted">Not shareable</span>
            <?php endif; ?>
            <form method="POST" action="inventory_delete.php" class="inline-form" onsubmit="return confirm('Delete this item?');">
              <input type="hidden" name="item_id" value="<?= (int) $item['item_id'] ?>">
              <button type="submit" class="btn btn-small btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
