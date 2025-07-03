 

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lengkapi Data Siswa - IT Learning</title>
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
      background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .data-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
    }
    
    .data-card {
      background: white;
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .data-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    
    .form-title {
      color: var(--dark-color);
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-align: center;
      font-size: 1.8rem;
      position: relative;
    }
    
    .form-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: linear-gradient(to right, var(--primary-color), var(--accent-color));
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
    
    .btn-submit {
      background: linear-gradient(to right, var(--primary-color), var(--accent-color));
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      font-weight: 600;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 6px rgba(67, 97, 238, 0.15);
    }
    
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(67, 97, 238, 0.2);
      background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
    }
    
    .progress-container {
      margin-bottom: 2rem;
    }
    
    .progress-text {
      display: flex;
      justify-content: space-between;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: #6c757d;
    }
    
    .progress-bar {
      height: 8px;
      border-radius: 4px;
      background-color: #e9ecef;
      overflow: hidden;
    }
    
    .progress-fill {
      height: 100%;
      width: 100%;
      background: linear-gradient(to right, var(--primary-color), var(--accent-color));
      border-radius: 4px;
      transition: width 0.5s ease;
    }
    
    @media (max-width: 768px) {
      .data-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="data-container">
    <div class="data-card">
      <div class="text-center mb-4">
        <a class="navbar-brand fw-bold text-primary" href="#" style="font-size: 1.8rem; letter-spacing: 1px; display: inline-block;">
          <i class="bi bi-lightbulb-fill me-2"></i> IT Learning
        </a>
        <h1 class="form-title mt-3">Lengkapi Data Siswa</h1>
      </div>
      
      <div class="progress-container">
        <div class="progress-text">
          <span> </span>
          <span> </span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
      </div>
      
      <form action="../php/simpan_data_siswa.php" method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-floating">
              <select name="pendidikan_terakhir" class="form-select" id="floatingPendidikan" required>
                <option value="">-- Pilih Pendidikan --</option>
                <option value="SD">SD</option>
                <option value="SMP">SMP</option>
                <option value="SMA">SMA/SMK</option>
                <option value="D3">Diploma (D3)</option>
                <option value="S1">Sarjana (S1)</option>
                <option value="S2">Magister (S2)</option>
              </select>
              <label for="floatingPendidikan"><i class="bi bi-mortarboard me-2"></i>Pendidikan Terakhir</label>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-floating">
              <input type="date" name="tanggal_lahir" class="form-control" id="floatingTanggalLahir" required>
              <label for="floatingTanggalLahir"><i class="bi bi-calendar-date me-2"></i>Tanggal Lahir</label>
            </div>
          </div>
          
          
          
          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary btn-submit w-100 py-3">
              <i class="bi bi-save-fill me-2"></i> Simpan Data
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>