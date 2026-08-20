<?php
// login.php
require_once __DIR__ . '/includes/auth.php';

if (currentUser()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$values = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM Users WHERE LOWER(email) = LOWER(?)");
    $stmt->execute([$values['email']]);
    $foundUser = $stmt->fetch();

    if (!$foundUser || !password_verify($password, $foundUser['password_hash'])) {
        $errors[] = "Invalid email or password.";
    } else {
        $_SESSION['user_id'] = (int) $foundUser['user_id'];
        header('Location: dashboard.php');
        exit;
    }
}

$title = "Login";
include __DIR__ . '/includes/header.php';
?>
<section class="form-card">
  <h1>Login</h1>
  <?php foreach ($errors as $e): ?>
    <div class="flash flash-error"><?= escapeHtml($e) ?></div>
  <?php endforeach; ?>
  <form method="POST" action="login.php">
    <label>Email
      <input type="email" name="email" required value="<?= escapeHtml($values['email']) ?>">
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button type="submit" class="btn btn-primary">Login</button>
  </form>
  <p>New here? <a href="register.php">Create an account</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
