<?php
// register.php
require_once __DIR__ . '/includes/auth.php';

if (currentUser()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$values = ['name' => '', 'email' => '', 'area' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name']  = trim($_POST['name'] ?? '');
    $values['email'] = trim($_POST['email'] ?? '');
    $values['area']  = trim($_POST['area'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($values['name'] === '' || $values['email'] === '' || strlen($password) < 6) {
        $errors[] = "Please fill all required fields; password must be at least 6 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM Users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$values['email']]);
        if ($stmt->fetch()) {
            $errors[] = "An account with that email already exists.";
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "INSERT INTO Users (name, email, password_hash, area) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$values['name'], $values['email'], $hash, $values['area']]);
        $_SESSION['user_id'] = (int) $pdo->lastInsertId();
        header('Location: dashboard.php');
        exit;
    }
}

$title = "Register";
include __DIR__ . '/includes/header.php';
?>
<section class="form-card">
  <h1>Create an account</h1>
  <?php foreach ($errors as $e): ?>
    <div class="flash flash-error"><?= escapeHtml($e) ?></div>
  <?php endforeach; ?>
  <form method="POST" action="register.php">
    <label>Name
      <input type="text" name="name" required value="<?= escapeHtml($values['name']) ?>">
    </label>
    <label>Email
      <input type="email" name="email" required value="<?= escapeHtml($values['email']) ?>">
    </label>
    <label>Area / Neighborhood
      <input type="text" name="area" placeholder="e.g., Dhanmondi" value="<?= escapeHtml($values['area']) ?>">
    </label>
    <label>Password
      <input type="password" name="password" required minlength="6">
    </label>
    <button type="submit" class="btn btn-primary">Register</button>
  </form>
  <p>Already have an account? <a href="login.php">Login</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
