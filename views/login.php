<?php
session_start();

// Kalau user SUDAH login
if (isset($_SESSION['user_id'])) {
    if (!empty($_SESSION['last_page_before_login'])) {
        header('Location: ' . $_SESSION['last_page_before_login']);
    } else {
        // Kalau gak ada history (misal akses langsung tanpa buka halaman lain sebelumnya)
     }
    exit;
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sistem Kursus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4895ef;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      min-height: 100vh;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
    }
    
    .login-card {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      width: 100%;
      max-width: 420px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .login-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    .app-logo {
      width: 80px;
      height: 80px;
      margin: 0 auto 1.5rem;
      display: block;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }
    
    .login-title {
      color: var(--dark-color);
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-align: center;
      font-size: 1.8rem;
    }
    
    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }
    
    .input-group-text {
      background-color: transparent;
      border-right: 0;
    }
    
    .form-floating>label {
      padding: 0.75rem 1rem;
    }
    
    .btn-login {
      background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(67, 97, 238, 0.15);
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(67, 97, 238, 0.2);
    }
    
    .divider {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
      color: #adb5bd;
    }
    
    .divider::before, .divider::after {
      content: "";
      flex: 1;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .divider::before {
      margin-right: 1rem;
    }
    
    .divider::after {
      margin-left: 1rem;
    }
    
    .social-login {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }
    
    .social-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      transition: all 0.3s ease;
    }
    
    .social-btn:hover {
      transform: translateY(-3px);
    }
    
    .google {
      background-color: #db4437;
    }
    
    .facebook {
      background-color: #4267b2;
    }
    
    .apple {
      background-color: #000000;
    }
    
    .footer-links {
      display: flex;
      justify-content: space-between;
      margin-top: 1.5rem;
      font-size: 0.9rem;
    }
    
    .footer-links a {
      color: #6c757d;
      text-decoration: none;
      transition: color 0.2s ease;
    }
    
    .footer-links a:hover {
      color: var(--primary-color);
    }
    
    @media (max-width: 576px) {
      .login-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
         <div class="text-center mb-4">
        <a class="navbar-brand fw-bold text-primary" href="#" style="font-size: 1.8rem; letter-spacing: 1px; display: inline-block;">
          <i class="bi bi-lightbulb-fill me-2"></i> IT Learning
        </a>
       </div>     
      <form action="../php/proses_login.php" method="POST">
        <div class="mb-3">
          <div class="form-floating">
            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" required>
            <label for="floatingInput">Alamat Email</label>
          </div>
        </div>
        
        <div class="mb-3">
          <div class="form-floating">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
            <label for="floatingPassword">Kata Sandi</label>
          </div>
          <div class="text-end mt-2">
            <a href="forgot-password.html" class="text-decoration-none small">Lupa kata sandi?</a>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
          <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
        </button>
        
        <div class="form-check text-start mb-3">
          <input class="form-check-input" type="checkbox" value="" id="rememberMe">
          <label class="form-check-label small" for="rememberMe">
            Ingat saya
          </label>
        </div>
      </form>
      
      <div class="divider">atau lanjutkan dengan</div>
      
      <div class="social-login">
        <a href="#" class="social-btn google" title="Google">
          <i class="bi bi-google"></i>
        </a>
        <a href="#" class="social-btn facebook" title="Facebook">
          <i class="bi bi-facebook"></i>
        </a>
        <a href="#" class="social-btn apple" title="Apple">
          <i class="bi bi-apple"></i>
        </a>
      </div>
      
      <div class="text-center">
        Belum punya akun? <a href="registar.php" class="text-decoration-none fw-bold">Daftar sekarang</a>
      </div>
      
      <div class="footer-links">
        <a href="#">Ketentuan Layanan</a>
        <a href="#">Kebijakan Privasi</a>
        <a href="#">Bantuan</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>