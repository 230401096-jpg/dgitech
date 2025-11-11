<?php
$page_title = "Login - DGITECH";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/config.php';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    if (empty($password)) {
        $errors[] = "Password harus diisi.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email AND is_active = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            // sukses login
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: /dashboard-admin.php");
            } elseif ($user['role'] === 'mitra') {
                header("Location: /dashboard-mitra.php");
            } else {
                header("Location: /dashboard-customer.php");
            }
            exit;
        } else {
            $errors[] = "Email atau password salah.";
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

<section class="login container">
    <h2>Login Akun</h2>
    <form method="post" action="login.php">
        <div class="form-group mt-2">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email)?htmlspecialchars($email):''; ?>">
        </div>
        <div class="form-group mt-2">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary mt-3">Login</button>
    </form>
    <p class="mt-3">Belum punya akun? <a href="register.php">Register di sini</a></p>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
