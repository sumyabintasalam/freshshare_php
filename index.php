<?php
// index.php
require_once __DIR__ . '/includes/auth.php';

$title = "Home";
$user = currentUser();
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <h1>Track what you have. Share what you won't finish.</h1>
  <p>FreshShare helps you keep tabs on your pantry's expiration dates and lets you
     pass on surplus food to neighbors before it goes to waste.</p>
  <?php if ($user): ?>
    <a class="btn btn-primary" href="dashboard.php">Go to My Inventory</a>
  <?php else: ?>
    <a class="btn btn-primary" href="register.php">Get Started</a>
    <a class="btn btn-secondary" href="login.php">Login</a>
  <?php endif; ?>
</section>
<section class="features">
  <div class="feature-card"><h3>Log Items</h3><p>Add pantry items with quantity and expiration date.</p></div>
  <div class="feature-card"><h3>Auto Status</h3><p>Items are auto-tagged Fresh, Expiring Soon, or Expired.</p></div>
  <div class="feature-card"><h3>Share Locally</h3><p>List surplus items so neighbors can claim them before they spoil.</p></div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
