<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booking_futsal";

$message = '';
$message_type = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception('Koneksi database gagal: ' . $conn->connect_error);
        }
        
        // Set charset
        $conn->set_charset("utf8");
        
        // Get and sanitize input data
        $nama = trim($_POST['nama'] ?? '');
        $gmail = trim($_POST['gmail'] ?? '');
        $umur = (int)($_POST['umur'] ?? 0);
        $user_password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirmPassword'] ?? '';
        $terms = isset($_POST['terms']);
        
        // Validation
        if (empty($nama)) {
            throw new Exception('Nama tidak boleh kosong');
        }
        
        if (empty($gmail)) {
            throw new Exception('Email tidak boleh kosong');
        }
        
        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }
        
        if ($umur < 1 || $umur > 100) {
            throw new Exception('Umur harus antara 1-100 tahun');
        }
        
        if (strlen($user_password) < 6) {
            throw new Exception('Password minimal 6 karakter');
        }
        
        if ($user_password !== $confirm_password) {
            throw new Exception('Password dan konfirmasi password tidak sama');
        }
        
        if (!$terms) {
            throw new Exception('Anda harus menyetujui syarat dan ketentuan');
        }
        
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE gmail = ?");
        $check_email->bind_param("s", $gmail);
        $check_email->execute();
        $result = $check_email->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception('Email sudah terdaftar');
        }
        
        // Hash password
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (nama, gmail, umur, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $nama, $gmail, $umur, $hashed_password);
        
        if ($stmt->execute()) {
            $message = 'Pendaftaran berhasil! Anda dapat login sekarang.';
            $message_type = 'success';
            
            // Clear form data
            $nama = $gmail = $umur = '';
        } else {
            throw new Exception('Gagal menyimpan data ke database');
        }
        
        $stmt->close();
        $check_email->close();
        $conn->close();
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Booking Futsal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: linear-gradient(90deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            text-align: center;
            padding: 2rem 1.5rem 1rem;
        }
        
        .soccer-icon {
            background: #27ae60;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            animation: rotate 3s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .register-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .register-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
            margin-bottom: 0;
        }
        
        .form-floating-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control-custom:focus {
            border-color: #e74c3c;
            box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
            background: white;
        }
        
        .input-group-custom {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .form-control-custom.with-icon {
            padding-left: 50px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .btn-register {
            background: linear-gradient(45deg, #27ae60, #229954);
            border: none;
            color: white;
            padding: 15px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.4);
            background: linear-gradient(45deg, #229954, #27ae60);
            color: white;
        }
        
        .login-link {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        .divider {
            position: relative;
            text-align: center;
            margin: 2rem 0;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #dee2e6;
        }
        
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .terms-check {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-check-custom .form-check-input:checked {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements::before {
            top: 15%;
            left: 8%;
            animation-delay: 0s;
        }
        
        .floating-elements::after {
            top: 75%;
            right: 12%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        .alert {
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements"></div>
    
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="index.html">
                <i class="fas fa-futbol me-2"></i>
                Futsal Booking
            </a>
        </div>
    </nav>

    <section class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 76px); padding: 2rem 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="register-card">
                        <div class="card-header-custom">
                            <div class="soccer-icon">
                                <i class="fas fa-user-plus text-white fs-4"></i>
                            </div>
                            <h1 class="register-title">Daftar Akun Baru</h1>
                            <p class="register-subtitle">Bergabunglah dengan komunitas futsal kami</p>
                        </div>
                        
                        <div class="p-4">
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                
                                <?php if ($message_type === 'success'): ?>
                                    <div class="text-center mb-3">
                                        <a href="login.php" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt me-2"></i>
                                            Login Sekarang
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" class="form-control form-control-custom with-icon" 
                                               id="nama" name="nama" placeholder="Nama Lengkap" 
                                               value="<?php echo isset($nama) ? htmlspecialchars($nama) : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" class="form-control form-control-custom with-icon" 
                                               id="gmail" name="gmail" placeholder="Email" 
                                               value="<?php echo isset($gmail) ? htmlspecialchars($gmail) : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input type="number" class="form-control form-control-custom with-icon" 
                                               id="umur" name="umur" placeholder="Umur" min="1" max="100" 
                                               value="<?php echo isset($umur) ? $umur : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" class="form-control form-control-custom with-icon" 
                                               id="password" name="password" placeholder="Password" required>
                                    </div>
                                </div>
                                
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" class="form-control form-control-custom with-icon" 
                                               id="confirmPassword" name="confirmPassword" placeholder="Konfirmasi Password" required>
                                    </div>
                                </div>
                                
                                <div class="terms-check">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label text-muted" for="terms">
                                            Saya setuju dengan <a href="#" class="text-decoration-none">Syarat & Ketentuan</a> dan <a href="#" class="text-decoration-none">Kebijakan Privasi</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-register w-100">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Daftar Sekarang
                                </button>
                                
                                <div class="divider">
                                    <span>atau</span>
                                </div>
                                
                                <div class="text-center">
                                    <p class="mb-0 text-muted">
                                        Sudah punya akun? 
                                        <a href="login.php" class="login-link">Masuk di sini</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time password confirmation check
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.style.borderColor = '#dc3545';
            } else if (confirmPassword && password === confirmPassword) {
                this.style.borderColor = '#28a745';
            } else {
                this.style.borderColor = '#e9ecef';
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
    </script>
</body>
</html>