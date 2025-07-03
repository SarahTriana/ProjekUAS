 


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lengkapi Data Pengajar - IT Learning</title>
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
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      min-height: 100vh;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .teacher-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
    }
    
    .teacher-card {
      background: white;
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 700px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .teacher-card:hover {
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
    
    .rating-stars {
      display: flex;
      justify-content: center;
      margin: 1rem 0;
    }
    
    .rating-stars input {
      display: none;
    }
    
    .rating-stars label {
      font-size: 2rem;
      color: #ddd;
      cursor: pointer;
      transition: color 0.2s;
    }
    
    .rating-stars input:checked ~ label {
      color: #ffc107;
    }
    
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
      color: #ffc107;
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
      .teacher-card {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <div class="teacher-container">
    <div class="teacher-card">
      <div class="text-center mb-4">
        <a class="navbar-brand fw-bold text-primary" href="#" style="font-size: 1.8rem; letter-spacing: 1px; display: inline-block;">
          <i class="bi bi-lightbulb-fill me-2"></i> IT Learning
        </a>
        <h1 class="form-title mt-3">Lengkapi Data Pengajar</h1>
      </div>
      
      <div class="progress-container">
        <div class="progress-text">
          <span>Profil Anda</span>
          <span>Lengkapi</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill"></div>
        </div>
      </div>
      
      <form action="../php/simpan_data_pengajar.php" method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="form-floating">
              <input type="text" name="spesialisasi" class="form-control" id="floatspesialisasi" min="0" max="50" required>
              <label for="floatspesialisasi"><i class="bi bi-tags me-2"></i>Spesialisasi</label>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="form-floating">
              <input type="number" name="pengalaman_mengajar_tahun" class="form-control" id="floatingPengalaman" min="0" max="50" required>
              <label for="floatingPengalaman"><i class="bi bi-clock-history me-2"></i>Pengalaman Mengajar (tahun)</label>
            </div>
          </div>
          
          
          
         
          
          <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary btn-submit w-100 py-3">
              <i class="bi bi-save-fill me-2"></i> Simpan Data Pengajar
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Script untuk rating stars
    document.querySelectorAll('.rating-stars input').forEach((radio, index, radios) => {
      radio.addEventListener('change', () => {
        // Highlight selected star and all previous stars
        radios.forEach((r, i) => {
          if (i <= index) {
            r.nextElementSibling.style.color = '#ffc107';
          }
        });
      });
    });
  </script>
</body>
</html>