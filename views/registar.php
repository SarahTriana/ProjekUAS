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
  <title>Registrasi - Sistem Kursus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4895ef;
      --success-color: #4cc9f0;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      min-height: 100vh;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .register-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
    }
    
    .register-card {
      background: rgba(255, 255, 255, 0.98);
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      width: 100%;
      max-width: 600px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .register-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    .app-logo {
      width: 100px;
      height: 100px;
      margin: 0 auto 1.5rem;
      display: block;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }
    
    .register-title {
      color: var(--dark-color);
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-align: center;
      font-size: 1.8rem;
      position: relative;
    }
    
    .register-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(to right, var(--primary-color), var(--success-color));
      border-radius: 2px;
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
    
    .form-floating>label {
      padding: 0.75rem 1rem;
      color: #6c757d;
    }
    
    .btn-register {
      background: linear-gradient(to right, var(--primary-color), var(--success-color));
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(67, 97, 238, 0.15);
    }
    
    .btn-register:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(67, 97, 238, 0.2);
      background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
    }
    
    .password-strength {
      height: 4px;
      background: #e9ecef;
      border-radius: 2px;
      margin-top: 0.5rem;
      overflow: hidden;
    }
    
    .password-strength-bar {
      height: 100%;
      width: 0%;
      transition: width 0.3s ease, background-color 0.3s ease;
    }
    
    .password-hint {
      font-size: 0.75rem;
      color: #6c757d;
      margin-top: 0.25rem;
    }
    
    .role-icon {
      font-size: 1.5rem;
      margin-right: 0.5rem;
      color: var(--primary-color);
    }
    
    .login-link {
      color: var(--primary-color);
      font-weight: 600;
      text-decoration: none;
      transition: color 0.2s ease;
    }
    
    .login-link:hover {
      color: var(--secondary-color);
      text-decoration: underline;
    }
    
    @media (max-width: 768px) {
      .register-card {
        padding: 1.5rem;
      }
      
      .app-logo {
        width: 80px;
        height: 80px;
      }
    }
  </style>
</head>
<body>

  <div class="register-container">
    <div class="register-card">
      <div class="text-center">
        <a class="navbar-brand fw-bold text-primary" href="#" style="font-size: 1.8rem; letter-spacing: 1px; display: inline-block;">
          <i class="bi bi-lightbulb-fill me-2"></i> IT Learning
        </a>
        <h1 class="register-title mt-3">Buat Akun Baru</h1>
      </div>
      
      <form action="../php/proses_registar.php" method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" name="nama_lengkap" class="form-control" id="floatingNama" placeholder="Nama Lengkap" required>
              <label for="floatingNama"><i class="bi bi-person-fill me-2"></i>Nama Lengkap</label>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-floating">
              <input type="tel" name="telepon" class="form-control" id="floatingTelepon" placeholder="Nomor Telepon">
              <label for="floatingTelepon"><i class="bi bi-telephone-fill me-2"></i>Nomor Telepon</label>
            </div>
          </div>
          
          <div class="col-12">
            <div class="form-floating">
              <textarea name="alamat" class="form-control" id="floatingAlamat" placeholder="Alamat" style="height: 100px"></textarea>
              <label for="floatingAlamat"><i class="bi bi-house-door-fill me-2"></i>Alamat</label>
            </div>
          </div>
          
          <div class="col-12">
            <div class="form-floating">
              <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="Email" required>
              <label for="floatingEmail"><i class="bi bi-envelope-fill me-2"></i>Email</label>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-floating">
              <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required oninput="checkPasswordStrength(this.value)">
              <label for="floatingPassword"><i class="bi bi-lock-fill me-2"></i>Password</label>
            </div>
            <div class="password-strength mt-2">
              <div class="password-strength-bar" id="passwordStrengthBar"></div>
            </div>
            <div class="password-hint">
              <small>Gunakan minimal 8 karakter dengan kombinasi huruf dan angka</small>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-floating">
              <input type="password" name="confirm_password" class="form-control" id="floatingConfirmPassword" placeholder="Konfirmasi Password" required>
              <label for="floatingConfirmPassword"><i class="bi bi-lock-fill me-2"></i>Konfirmasi Password</label>
            </div>
          </div>
          
          <div class="col-12">
            <div class="form-floating">
              <select name="role" class="form-select" id="floatingRole" required>
                <option value="">-- Pilih Peran --</option>
                <option value="siswa"><i class="bi bi-person-fill"></i> Siswa</option>
                <option value="pengajar"><i class="bi bi-person-badge-fill"></i> Pengajar</option>
              </select>
              <label for="floatingRole"><i class="bi bi-person-rolodex me-2"></i>Pilih Peran</label>
            </div>
          </div>
          
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="agreeTerms" required>
              <label class="form-check-label small" for="agreeTerms">
                Saya menyetujui <a href="#" class="text-primary">syarat dan ketentuan</a> serta <a href="#" class="text-primary">kebijakan privasi</a>
              </label>
            </div>
          </div>
          
          <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary btn-register w-100 py-3">
              <i class="bi bi-person-plus-fill me-2"></i> Daftar Sekarang
            </button>
          </div>
        </div>
      </form>
      
      <div class="text-center mt-4">
        <p class="mb-0">Sudah punya akun? <a href="login.html" class="login-link">Login di sini</a></p>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function checkPasswordStrength(password) {
      const strengthBar = document.getElementById('passwordStrengthBar');
      let strength = 0;
      
      // Check length
      if (password.length >= 6) strength += 1;
      if (password.length >= 8) strength += 1;
      
       if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
      
       if (password.match(/[0-9]/)) strength += 1;
      
      // Check for special chars
      if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
      
      // Update bar
      switch(strength) {
        case 0:
          strengthBar.style.width = '0%';
          strengthBar.style.backgroundColor = '#e9ecef';
          break;
        case 1:
          strengthBar.style.width = '20%';
          strengthBar.style.backgroundColor = '#dc3545';
          break;
        case 2:
          strengthBar.style.width = '40%';
          strengthBar.style.backgroundColor = '#fd7e14';
          break;
        case 3:
          strengthBar.style.width = '60%';
          strengthBar.style.backgroundColor = '#ffc107';
          break;
        case 4:
          strengthBar.style.width = '80%';
          strengthBar.style.backgroundColor = '#28a745';
          break;
        case 5:
          strengthBar.style.width = '100%';
          strengthBar.style.backgroundColor = '#20c997';
          break;
      }
    }
  </script>
</body>
</html>