<?php
session_start();
$register_error = "";
$register_success = "";

// Proses register jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "tugas_besar");

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validasi
    if ($password !== $confirm) {
        $register_error = "Konfirmasi password tidak cocok!";
    } else {
        // Cek email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $register_error = "Email sudah terdaftar!";
        } else {
            // Hash password dan simpan user baru
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            if ($stmt->execute()) {
                $register_success = "Registrasi berhasil! Silakan login.";
            } else {
                $register_error = "Registrasi gagal. Coba lagi.";
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>d'edge coffee - Register Kasir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>d'edge coffee</h1>
        <p class="subtitle">Sign up or sign in to your account</p>
        <?php if ($register_error): ?>
            <div class="error"><?= $register_error ?></div>
        <?php endif; ?>
        <?php if ($register_success): ?>
            <div class="success" style="background:#e0ffe0;color:#080;padding:8px;border-radius:6px;margin-bottom:12px;"><?= $register_success ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Name</label>
            <input type="text" name="name" required>
            <label>Email Address</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <label>Confirm Password</label>
            <input type="password" name="confirm" required>
            <button type="submit">Sign Up</button>
        </form>
        <p style="margin-top:18px;font-size:0.98rem;">Sudah punya akun? <a href="login.php" style="color:#d2691e;text-decoration:none;">Sign In</a></p>
    </div>
</body>
</html>