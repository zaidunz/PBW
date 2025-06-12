<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booking_futsal";

$message = '';
$message_type = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Process login form
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
        
        // Get input data
        $gmail = trim($_POST['gmail'] ?? '');
        $user_password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($gmail)) {
            throw new Exception('Email tidak boleh kosong');
        }
        
        if (empty($user_password)) {
            throw new Exception('Password tidak boleh kosong');
        }
        
        // Get user from database
        $stmt = $conn->prepare("SELECT id, nama, gmail, password FROM users WHERE gmail = ?");
        $stmt->bind_param("s", $gmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Email tidak ditemukan');
        }
        
        $user = $result->fetch_assoc();
        
        // Verify password
        if (!password_verify($user_password, $user['password'])) {
            throw new Exception('Password salah');
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_gmail'] = $user['gmail'];
        
        // Redirect to index
        header('Location: index.php');
        exit();
        
        $stmt->close();
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
    <title>Login - Booking Futsal</title>
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
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 450px;
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
        
        .login-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
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
        
        .btn-login {
            background: linear-gradient(45deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 15px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            background: linear-gradient(45deg, #2980b9, #3498db);
            color: white;
        }
        
        .register-link {
            color: #27ae60;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link:hover {
            color: #229954;
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
                    <div class="login-card">
                        <div class="card-header-custom">
                            <div class="soccer-icon">
                                <i class="fas fa-sign-in-alt text-white fs-4"></i>
                            </div>
                            <h1 class="login-title">Selamat Datang</h1>
                            <p class="login-subtitle">Masuk ke akun Anda</p>
                        </div>
                        
                        <div class="p-4">
                            <?php if (!empty($message)): ?>
                                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars($message); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="">
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" class="form-control form-control-custom with-icon" 
                                               id="gmail" name="gmail" placeholder="Email" 
                                               value="<?php echo isset($_POST['gmail']) ? htmlspecialchars($_POST['gmail']) : ''; ?>" required>
                                    </div>
                                </div>
                                
                                <div class="form-floating-custom">
                                    <div class="input-group-custom">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" class="form-control form-control-custom with-icon" 
                                               id="password" name="password" placeholder="Password" required>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label text-muted" for="remember">
                                            Ingat saya
                                        </label>
                                    </div>
                                    <a href="#" class="text-decoration-none" style="color: #6c757d; font-size: 0.9rem;">
                                        Lupa password?
                                    </a>
                                </div>
                                
                                <button type="submit" class="btn btn-login w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Masuk
                                </button>
                                
                                <div class="divider">
                                    <span>atau</span>
                                </div>
                                
                                <div class="text-center">
                                    <p class="mb-0 text-muted">
                                        Belum punya akun? 
                                        <a href="register.php" class="register-link">Daftar sekarang</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>