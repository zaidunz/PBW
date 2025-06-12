<?php
include 'db.php';
session_start();

// Cek login
if (!isset($_SESSION['gmail'])) {
    header('Location: login.php');
    exit;
}

// Ambil data POST
extract($_POST);

// Validasi data yang diperlukan
if (!isset($lapangan, $tanggal, $jam, $durasi, $nama, $nohp)) {
    header('Location: booking.php');
    exit;
}

// Hitung harga
$prices = [
    'Lapangan 1' => ['day' => 100000, 'night' => 120000], 
    'Lapangan 2' => ['day' => 125000, 'night' => 140000]
];
$period = $jam >= 18 ? 'night' : 'day';
$harga = $prices[$lapangan][$period];
$total = $harga * $durasi;

// Proses pembayaran
if (isset($_POST['bayar'])) {
    // Validasi file upload
    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        $error = "Harap upload bukti pembayaran!";
    } else {
        // Buat direktori uploads jika belum ada
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        // Generate nama file unik
        $file_extension = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $upload_path = 'uploads/' . $new_filename;
        
        // Upload file
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $upload_path)) {
            // Insert ke database dengan parameter binding yang benar
            $stmt = $conn->prepare(
                "INSERT INTO bookings (nama, nohp, lapangan, tanggal, jam, durasi, harga_per_jam, metode, bukti, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'lunas')"
            );
            
            // Bind parameter: s=string, i=integer
            // nama(s), nohp(s), lapangan(s), tanggal(s), jam(i), durasi(i), harga(i), metode(s), bukti(s)
            $stmt->bind_param('ssssiiiss', 
                $nama, 
                $nohp, 
                $lapangan, 
                $tanggal, 
                $jam, 
                $durasi, 
                $harga, 
                $_POST['metode'], 
                $new_filename
            );
            
            if ($stmt->execute()) {
                // Berhasil, redirect dengan pesan sukses
                $_SESSION['success'] = "Pembayaran berhasil! Booking Anda telah dikonfirmasi.";
                header('Location: booking.php');
                exit;
            } else {
                $error = "Gagal menyimpan data booking: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Gagal mengupload file bukti pembayaran!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Pembayaran - Booking Futsal</title>
    <style>
        .bg-primary-custom {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }
        .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
        }
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-futbol"></i> Booking Futsal
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="booking.php">Kembali ke Booking</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="card mx-auto" style="max-width:600px">
            <div class="card-header bg-primary-custom text-white text-center">
                <h4 class="mb-0">Konfirmasi Pembayaran</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Detail Booking -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="text-primary mb-3">Detail Booking</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nama Penyewa:</strong></td>
                                <td><?= htmlspecialchars($nama) ?></td>
                            </tr>
                            <tr>
                                <td><strong>No. Telepon:</strong></td>
                                <td><?= htmlspecialchars($nohp) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Lapangan:</strong></td>
                                <td><?= htmlspecialchars($lapangan) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal:</strong></td>
                                <td><?= date('d/m/Y', strtotime($tanggal)) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Waktu:</strong></td>
                                <td><?= sprintf('%02d:00', $jam) ?> - <?= sprintf('%02d:00', $jam + $durasi) ?> (<?= $durasi ?> jam)</td>
                            </tr>
                            <tr>
                                <td><strong>Periode:</strong></td>
                                <td>
                                    <span class="badge <?= $period == 'day' ? 'bg-warning' : 'bg-info' ?>">
                                        <?= $period == 'day' ? 'Siang (06:00-17:59)' : 'Malam (18:00-05:59)' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Harga per Jam:</strong></td>
                                <td>Rp <?= number_format($harga, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total Pembayaran:</strong></td>
                                <td><strong class="text-primary">Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Form Pembayaran -->
                <form method="POST" enctype="multipart/form-data" id="paymentForm">
                    <!-- Hidden inputs untuk menjaga data -->
                    <input type="hidden" name="lapangan" value="<?= htmlspecialchars($lapangan) ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <input type="hidden" name="jam" value="<?= $jam ?>">
                    <input type="hidden" name="durasi" value="<?= $durasi ?>">
                    <input type="hidden" name="nama" value="<?= htmlspecialchars($nama) ?>">
                    <input type="hidden" name="nohp" value="<?= htmlspecialchars($nohp) ?>">

                    <h5 class="text-primary mb-3">Metode Pembayaran</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">Pilih Metode Pembayaran</label>
                        <select name="metode" class="form-select" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="BCA">🏦 BCA - 1234567890 (a.n. Futsal Center)</option>
                            <option value="BRI">🏦 BRI - 0987654321 (a.n. Futsal Center)</option>
                            <option value="BNI">🏦 BNI - 1122334455 (a.n. Futsal Center)</option>
                            <option value="Mandiri">🏦 Mandiri - 1357924680 (a.n. Futsal Center)</option>
                            <option value="Dana">💳 Dana - 089604224173</option>
                            <option value="OVO">💳 OVO - 089604224173</option>
                            <option value="GoPay">💳 GoPay - 089604224173</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Upload Bukti Pembayaran</label>
                        <input type="file" name="bukti" class="form-control" accept="image/*" required>
                        <div class="form-text">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Upload screenshot atau foto bukti transfer. Format: JPG, PNG, maksimal 5MB.
                            </small>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Petunjuk Pembayaran:</strong>
                        <ol class="mb-0 mt-2">
                            <li>Transfer sesuai total pembayaran ke rekening yang dipilih</li>
                            <li>Screenshot/foto bukti transfer</li>
                            <li>Upload bukti pembayaran di form ini</li>
                            <li>Klik tombol "Konfirmasi Pembayaran"</li>
                        </ol>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="bayar" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card"></i> Konfirmasi Pembayaran
                        </button>
                        <a href="booking.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js"></script>
    <script>
        // Validasi form sebelum submit
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const metode = document.querySelector('select[name="metode"]').value;
            const bukti = document.querySelector('input[name="bukti"]').files[0];
            
            if (!metode) {
                e.preventDefault();
                alert('Harap pilih metode pembayaran!');
                return;
            }
            
            if (!bukti) {
                e.preventDefault();
                alert('Harap upload bukti pembayaran!');
                return;
            }
            
            // Validasi ukuran file (5MB)
            if (bukti.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                return;
            }
            
            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(bukti.type)) {
                e.preventDefault();
                alert('Format file tidak didukung! Gunakan JPG atau PNG.');
                return;
            }
            
            // Konfirmasi sebelum submit
            if (!confirm('Apakah data pembayaran sudah benar? Setelah dikonfirmasi, booking tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
        
        // Preview file yang diupload
        document.querySelector('input[name="bukti"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Buat preview image jika diperlukan
                    let preview = document.getElementById('imagePreview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'imagePreview';
                        preview.className = 'mt-2';
                        e.target.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                        <p class="small text-muted mt-1">Preview: ${file.name}</p>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>