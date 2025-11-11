<?php
$page_title = "Register - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers/validation.php';

if (isset($_POST['submit'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role = 'customer'; // default role customer

    $errors = [];
    if (!is_valid_name($name)) {
        $errors[] = "Nama tidak valid.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    if ($password !== $confirm) {
        $errors[] = "Password dan konfirmasi tidak sama.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    if (empty($errors)) {
        // cek email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Email sudah terdaftar.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("INSERT INTO users (name, email, password, phone, role, created_at) VALUES (:name, :email, :pass, :phone, :role, NOW())");
            $stmt2->execute([
                ':name'  => $name,
                ':email' => $email,
                ':pass'  => $hash,
                ':phone' => $phone,
                ':role'  => $role
            ]);
            echo "<div class='alert alert-success'>Registrasi berhasil! <a href='login.php'>Login di sini</a></div>";
        }
    }

    if (!empty($errors)) {
        echo "<div class='alert alert-danger'><ul>";
        foreach ($errors as $e) {
            echo "<li>" . htmlspecialchars($e) . "</li>";
        }
        echo "</ul></div>";
    }
}
?>

<section class="register container">
    <h2>Register Akun</h2>
    <form method="post" action="register.php">
        <div class="form-group mt-2">
            <label for="name">Nama Lengkap</label>
            <input type="text" class="form-control" id="name" name="name" required value="<?php echo isset($name)?htmlspecialchars($name):''; ?>">
        </div>
        <div class="form-group mt-2">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email)?htmlspecialchars($email):''; ?>">
        </div>
        <div class="form-group mt-2">
            <label for="phone">Telepon</label>
            <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo isset($phone)?htmlspecialchars($phone):''; ?>">
        </div>
        <div class="form-group mt-2">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group mt-2">
            <label for="confirm_password">Konfirmasi Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary mt-3">Register</button>
    </form>
    <p class="mt-3">Sudah punya akun? <a href="login.php">Login di sini</a></p>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
