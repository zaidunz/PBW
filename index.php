<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Futsal</title>
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
        
        .main-card {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .card-header-custom {
            background: rgba(255,255,255,0.1);
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
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        .welcome-title {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .welcome-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
            margin-bottom: 0;
        }
        
        .card-body-custom {
            background: white;
            padding: 2rem 1.5rem;
        }
        
        .btn-custom-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-custom-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
            background: linear-gradient(45deg, #2980b9, #3498db);
            color: white;
        }
        
        .btn-custom-outline {
            background: transparent;
            border: 2px solid #e74c3c;
            color: #e74c3c;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-custom-outline:hover {
            background: #e74c3c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.3);
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            color: #666;
            display: flex;
            align-items: center;
        }
        
        .feature-list li i {
            color: #27ae60;
            margin-right: 10px;
            width: 20px;
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
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements::before {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-elements::after {
            top: 60%;
            right: 15%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="floating-elements"></div>
    
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#">
                <i class="fas fa-futbol me-2"></i>
                Futsal Booking
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="#"><i class="fas fa-user"></i></a>
            </div>
        </div>
    </nav>

    <section class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 76px); padding: 2rem 0;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="main-card">
                        <div class="card-header-custom">
                            <div class="soccer-icon">
                                <i class="fas fa-futbol text-white fs-4"></i>
                            </div>
                            <h1 class="welcome-title">Pilih Slot Booking</h1>
                            <p class="welcome-subtitle">Tentukan waktu terbaik untuk bermain futsal</p>
                        </div>
                        
                        <div class="card-body-custom">
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i> Booking mudah dan cepat</li>
                                <li><i class="fas fa-check-circle"></i> Lapangan berkualitas premium</li>
                                <li><i class="fas fa-check-circle"></i> Harga terjangkau</li>
                                <li><i class="fas fa-check-circle"></i> Lokasi strategis</li>
                            </ul>
                            
                            <div class="text-center">
                                <p class="text-muted mb-4">Silakan login atau daftar untuk melanjutkan booking</p>
                                <div class="d-grid gap-3">
                                    <a href="login.php" class="btn btn-custom-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </a>
                                    <a href="register.php" class="btn btn-custom-outline btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>Daftar Akun Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>