<?php
session_start();
$login_error = "";

// Proses login jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "tugas_besar");

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // Cek user di database
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Login sukses
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;

            // Remember me
            if ($remember) {
                setcookie("email", $email, time() + (86400 * 30), "/");
            } else {
                setcookie("email", "", time() - 3600, "/");
            }

            header("Location: dashboard.php");
            exit;
        } else {
            $login_error = "Password salah!";
        }
    } else {
        $login_error = "Email tidak ditemukan!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>d'edge coffee - Login Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>d'edge coffee</h1>
        <p class="subtitle">Sign in or sign up for an account</p>
        <?php if ($login_error): ?>
            <div class="error"><?= $login_error ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Email Address</label>
            <input type="email" name="email" required value="<?= isset($_COOKIE['email']) ? $_COOKIE['email'] : '' ?>">
            <label>Password</label>
            <input type="password" name="password" required>
            <div class="remember-me">
                <input type="checkbox" name="remember" id="remember" <?= isset($_COOKIE['email']) ? 'checked' : '' ?>>
                <label for="remember">Remember me</label>
            </div>
            <button type="submit">Sign In</button>
        </form>
    </div>
</body>
</html>