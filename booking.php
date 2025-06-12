<?php
include 'db.php'; 
session_start();
if(!isset($_SESSION['gmail'])) header('Location: login.php');

$lap = $_POST['lapangan'] ?? '';
$tgl = $_POST['tanggal'] ?? '';
$booked = [];

// Jika lapangan dan tanggal di-set, tampilkan jadwal lunas
if ($lap && $tgl) {
    $res = $conn->query("SELECT jam, durasi, nama FROM bookings WHERE lapangan='{$lap}' AND tanggal='{$tgl}' AND status='lunas'");
    while($r = $res->fetch_assoc()){
        for($i=0; $i<$r['durasi']; $i++){
            $hour = $r['jam'] + $i;
            $booked[$hour] = $r['nama'];
        }
    }
}

// Harga per lapangan untuk display
$prices = [
    'Lapangan 1' => ['day' => 100000, 'night' => 120000], 
    'Lapangan 2' => ['day' => 125000, 'night' => 140000]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Booking Futsal - Pilih Slot Terbaik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    
        :root {
            --primary-color: #dc3545;
            --primary-dark: #c82333;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
            box-shadow: 0 4px 20px rgba(220, 53, 69, 0.3);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header-custom::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.8rem;
            position: relative;
            z-index: 2;
        }

        .form-select, .form-control {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 15px;
            font-weight: 600;
            padding: 12px 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #5a6268);
            border: none;
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        .schedule-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 5px solid var(--primary-color);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .time-slot {
            display: inline-block;
            background: linear-gradient(135deg, var(--warning-color), #e0a800);
            color: #212529;
            padding: 8px 16px;
            margin: 4px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(255, 193, 7, 0.3);
            animation: bounce 0.5s ease-out;
        }

        @keyframes bounce {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .price-info {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(23, 162, 184, 0.05));
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .price-badge {
            background: linear-gradient(135deg, var(--info-color), #138496);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
        }

        .field-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--success-color), #218838);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin: 0 auto 1rem;
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-ball {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-ball:nth-child(1) {
            width: 60px;
            height: 60px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-ball:nth-child(2) {
            width: 40px;
            height: 40px;
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-ball:nth-child(3) {
            width: 80px;
            height: 80px;
            bottom: 10%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .status-indicator {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--success-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
        }
</style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-ball"></div>
        <div class="floating-ball"></div>
        <div class="floating-ball"></div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-futbol me-2"></i>Futsal Booking
            </a>
            <div class="navbar-nav ms-auto">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="main-card mx-auto" style="max-width: 700px;">
            <div class="card-header-custom">
                <div class="field-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4><i class="fas fa-clock me-2"></i>Pilih Slot Booking</h4>
                <p class="mb-0 mt-2" style="opacity: 0.9;">Tentukan waktu terbaik untuk bermain futsal</p>
            </div>
            
            <div class="card-body p-4">
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Form Pilih Lapangan dan Tanggal -->
                <form method="POST" class="mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>Pilih Lapangan
                            </label>
                            <select name="lapangan" class="form-select" required>
                                <option value="">-- Pilih Lapangan --</option>
                                <option value="Lapangan 1" <?= $lap=='Lapangan 1'?'selected':'' ?>>
                                    🥅 Lapangan 1 (Premium)
                                </option>
                                <option value="Lapangan 2" <?= $lap=='Lapangan 2'?'selected':'' ?>>
                                    ⚽ Lapangan 2 (Deluxe)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar text-primary me-2"></i>Pilih Tanggal
                            </label>
                            <input type="date" name="tanggal" class="form-control" value="<?= $tgl ?>" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <button type="submit" name="check" class="btn btn-secondary w-100">
                        <i class="fas fa-search me-2"></i>Cek Ketersediaan Jadwal
                    </button>
                </form>

                <!-- Info Harga -->
                <?php if($lap): ?>
                    <div class="price-info">
                        <h6 class="mb-3"><i class="fas fa-tags text-info me-2"></i>Informasi Harga - <?= $lap ?></h6>
                        <div class="d-flex justify-content-center flex-wrap">
                            <span class="price-badge">
                                <i class="fas fa-sun me-1"></i>Siang (06:00-17:59): Rp <?= number_format($prices[$lap]['day'], 0, ',', '.') ?>/jam
                            </span>
                            <span class="price-badge">
                                <i class="fas fa-moon me-1"></i>Malam (18:00-05:59): Rp <?= number_format($prices[$lap]['night'], 0, ',', '.') ?>/jam
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Hasil Jadwal -->
                <?php if($lap && $tgl): ?>
                    <div class="schedule-info">
                        <h6 class="mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Jadwal <?= $lap ?> - <?= date('d F Y', strtotime($tgl)) ?>
                        </h6>
                        <p class="mb-2"><strong>Jam yang sudah terbooked:</strong></p>
                        <div class="mb-3">
                            <?php if(!empty($booked)): ?>
                                <?php foreach($booked as $h => $u): ?>
                                    <span class="time-slot">
                                        <i class="fas fa-clock me-1"></i><?= sprintf('%02d:00', $h) ?> 
                                        <small>(<?= htmlspecialchars($u) ?>)</small>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="badge bg-success fs-6 p-2">
                                    <i class="fas fa-check-circle me-1"></i>Semua slot masih tersedia!
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Form Booking -->
                    <div class="card border-0" style="background: linear-gradient(135deg, rgba(220, 53, 69, 0.05), rgba(220, 53, 69, 0.02));">
                        <div class="card-body">
                            <h6 class="card-title text-center mb-4">
                                <i class="fas fa-edit text-primary me-2"></i>Form Booking
                            </h6>
                            
                            <form action="pembayaran.php" method="POST" id="bookingForm">
                                <input type="hidden" name="lapangan" value="<?= $lap ?>">
                                <input type="hidden" name="tanggal" value="<?= $tgl ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-clock text-primary me-2"></i>Jam Mulai
                                        </label>
                                        <select name="jam" class="form-select" required id="jamSelect">
                                            <option value="">-- Pilih Jam --</option>
                                            <?php for($h=0; $h<24; $h++): ?>
                                                <option value="<?= $h ?>" <?= isset($booked[$h])?'disabled':'' ?>>
                                                    <?= sprintf('%02d:00', $h) ?>
                                                    <?php if($h >= 6 && $h < 18): ?>
                                                        <span class="text-warning">☀️ Siang</span>
                                                    <?php else: ?>
                                                        <span class="text-info">🌙 Malam</span>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-hourglass-half text-primary me-2"></i>Durasi (Jam)
                                        </label>
                                        <select name="durasi" class="form-select" required id="durasiSelect">
                                            <option value="">-- Pilih Durasi --</option>
                                            <option value="1">1 Jam</option>
                                            <option value="2">2 Jam</option>
                                            <option value="3">3 Jam</option>
                                            <option value="4">4 Jam</option>
                                            <option value="5">5 Jam</option>
                                            <option value="6">6 Jam</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user text-primary me-2"></i>Nama Penyewa
                                        </label>
                                        <input type="text" name="nama" class="form-control" 
                                               placeholder="Masukkan nama lengkap" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-phone text-primary me-2"></i>No. Telepon
                                        </label>
                                        <input type="tel" name="nohp" class="form-control" 
                                               placeholder="08xxxxxxxxxx" required>
                                    </div>
                                </div>

                                <div id="estimasiHarga" class="alert alert-info" style="display: none;">
                                    <h6><i class="fas fa-calculator me-2"></i>Estimasi Biaya:</h6>
                                    <div id="detailHarga"></div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    <i class="fas fa-credit-card me-2"></i>Lanjut ke Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data harga dari PHP
        const prices = <?= json_encode($prices) ?>;
        const lapangan = "<?= $lap ?>";
        const bookedSlots = <?= json_encode($booked) ?>;

        // Hitung estimasi harga
        function updateEstimasi() {
            const jam = document.getElementById('jamSelect').value;
            const durasi = document.getElementById('durasiSelect').value;
            
            if (jam && durasi && lapangan) {
                const jamInt = parseInt(jam);
                const durasiInt = parseInt(durasi);
                
                // Cek apakah ada konflik dengan jam yang sudah dibook
                let hasConflict = false;
                for (let i = 0; i < durasiInt; i++) {
                    if (bookedSlots[jamInt + i]) {
                        hasConflict = true;
                        break;
                    }
                }
                
                if (hasConflict) {
                    document.getElementById('estimasiHarga').style.display = 'block';
                    document.getElementById('estimasiHarga').className = 'alert alert-danger';
                    document.getElementById('detailHarga').innerHTML = 
                        '<i class="fas fa-exclamation-triangle me-2"></i>Konflik dengan jadwal yang sudah ada!';
                    return;
                }
                
                const period = jamInt >= 18 || jamInt < 6 ? 'night' : 'day';
                const hargaPerJam = prices[lapangan][period];
                const total = hargaPerJam * durasiInt;
                
                document.getElementById('estimasiHarga').style.display = 'block';
                document.getElementById('estimasiHarga').className = 'alert alert-info';
                document.getElementById('detailHarga').innerHTML = `
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Periode:</strong> ${period === 'day' ? '☀️ Siang' : '🌙 Malam'}<br>
                            <strong>Harga/jam:</strong> Rp ${hargaPerJam.toLocaleString('id-ID')}
                        </div>
                        <div class="col-sm-6">
                            <strong>Durasi:</strong> ${durasiInt} jam<br>
                            <strong class="text-primary">Total: Rp ${total.toLocaleString('id-ID')}</strong>
                        </div>
                    </div>
                `;
            } else {
                document.getElementById('estimasiHarga').style.display = 'none';
            }
        }

        // Event listeners
        document.getElementById('jamSelect').addEventListener('change', updateEstimasi);
        document.getElementById('durasiSelect').addEventListener('change', updateEstimasi);

        // Validasi form sebelum submit
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const jam = parseInt(document.getElementById('jamSelect').value);
            const durasi = parseInt(document.getElementById('durasiSelect').value);
            
            // Cek konflik jadwal
            for (let i = 0; i < durasi; i++) {
                if (bookedSlots[jam + i]) {
                    e.preventDefault();
                    alert('Jam yang dipilih bertabrakan dengan booking yang sudah ada!');
                    return;
                }
            }
            
            // Cek jam tidak melebihi 24:00
            if (jam + durasi > 24) {
                e.preventDefault();
                alert('Durasi booking melebihi jam operasional (24:00)!');
                return;
            }
        });

        // Auto-hide success message
        setTimeout(function() {
            const alert = document.querySelector('.alert-success');
            if (alert) {
                alert.style.animation = 'fadeOut 0.5s ease-out forwards';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);

        // Set minimum date to today
        document.querySelector('input[name="tanggal"]').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>