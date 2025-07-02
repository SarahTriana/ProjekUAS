<?php
session_start();
include '../database/koneksi.php';

// Ambil input email dan password dari form
$email = $_POST['email'];
$password = $_POST['password'];

// Cek data pengguna berdasarkan email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Jika pengguna ditemukan
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verifikasi password
    if (password_verify($password, $user['password_hash'])) {
        // Simpan data ke session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        // Update waktu login terakhir
        $update = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $update->bind_param("i", $user['user_id']);
        $update->execute();

        // Redirect sesuai peran
        if ($user['role'] === 'admin' || $user['role'] === 'pengajar') {
            header("Location: ../views/dashboard/dashboard.php");
        } elseif ($user['role'] === 'siswa') {
            header("Location: ../views/index_user.php");
        } else {
            echo "Peran tidak dikenali.";
        }
        exit;
    }
}

// Tampilkan halaman error login
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Gagal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(135deg, #4361ee, #3f37c9);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }
    .alert-box {
      background-color: white;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    .alert-icon {
      font-size: 2rem;
      color: #dc3545;
    }
    .btn-primary {
      background: linear-gradient(to right, #4361ee, #3f37c9);
      border: none;
    }
    .btn-primary:hover {
      background: linear-gradient(to right, #3f37c9, #4361ee);
    }
  </style>
</head>
<body class="d-flex justify-content-center align-items-center">

  <div class="alert-box text-center">
    <div class="alert-icon mb-2">
      <i class="bi bi-x-circle-fill"></i>
    </div>
    <h4 class="alert-heading text-danger">Login Gagal!</h4>
    <p class="mb-3">Email atau password yang Anda masukkan salah.</p>
    <a href="../views/login.php" class="btn btn-primary">
      <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Login
    </a>
  </div>

</body>
</html>
