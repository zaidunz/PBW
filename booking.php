<?php
include 'db.php';
session_start();
if (!isset($_SESSION['gmail'])) {
    header('Location: login.php'); exit;
}
// Harga
$prices = [
    'Lapangan 1' => ['day'=>100000,'night'=>120000],
    'Lapangan 2' => ['day'=>125000,'night'=>140000]
];
$booked = [];
$lapangan = $_POST['lapangan'] ?? '';
$tanggal = $_POST['tanggal'] ?? '';
if (isset($_POST['check'])) {
    $jadwal = $conn->query("SELECT jam,durasi FROM bookings WHERE lapangan='$lapangan' AND tanggal='$tanggal'");
    while ($r = $jadwal->fetch_assoc()) {
        for ($i=0; $i<$r['durasi']; $i++) $booked[] = $r['jam']+$i;
    }
}
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $jam = (int)$_POST['jam'];
    $durasi = (int)$_POST['durasi'];
    $period = $jam>=18?'night':'day';
    $harga = $prices[$lapangan][$period];
    $total = $harga*$durasi;
    $bukti = $_FILES['bukti']['name'];
    move_uploaded_file($_FILES['bukti']['tmp_name'], 'uploads/'.$bukti);
    $conflict = $conn->query(
        "SELECT 1 FROM bookings WHERE lapangan='$lapangan' AND tanggal='$tanggal' AND ( (jam<=$jam AND jam+durasi>$jam) OR (jam<($jam+$durasi) AND jam+durasi>=($jam+$durasi)) )"
    )->num_rows;
    if (!$conflict) {
        $stmt = $conn->prepare(
            "INSERT INTO bookings(nama,lapangan,tanggal,jam,durasi,harga_per_jam,bukti,status) VALUES(?,?,?,?,?,?,?, 'lunas')"
        );
        $stmt->bind_param('sssiiis', $nama,$lapangan,$tanggal,$jam,$durasi,$harga,$bukti);
        $stmt->execute();
        $success = "Booking berhasil. Total: Rp".number_format($total,0,',','.');
        // Clear for fresh state
        $booked = [];
    } else {
        $error = 'Waktu sudah dibooking';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Booking Lapangan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php"><img src="assets/logo.png" width="40" height="40"> Booking Futsal</a>
  </div>
</nav>
<div class="container py-5">
  <div class="card mx-auto shadow" style="max-width:800px">
    <div class="card-body">
      <h3 class="text-center mb-4">Booking Lapangan</h3>
      <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>";
            elseif(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <!-- Form Pilih Lapangan & Tanggal -->
      <form method="POST" class="mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <select name="lapangan" class="form-select" required>
              <option value="">Pilih Lapangan</option>
              <?php foreach($prices as $p=>$v): ?>
                <option value="<?=$p?>" <?= $lapangan==$p?'selected':''?>><?=$p?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <input type="date" name="tanggal" class="form-control" value="<?=$tanggal?>" required>
          </div>
        </div>
        <button name="check" class="btn btn-outline-primary mt-3 w-100">Cek Jadwal</button>
      </form>
      <?php if($lapangan && $tanggal): ?>
        <p><strong>Harga per jam:</strong> Rp<?=number_format($prices[$lapangan][($booked? ($jam>=18?'night':'day') : 'day')],0,',','.')?></p>
        <p><strong>Jam terisi:</strong> <?= $booked? implode(', ',$booked): 'Tidak ada'?></p>
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="lapangan" value="<?=$lapangan?>">
          <input type="hidden" name="tanggal" value="<?=$tanggal?>">
          <div class="mb-3 form-floating">
            <input type="text" name="nama" class="form-control" placeholder="Nama Penyewa" required>
            <label>Nama Penyewa</label>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <select name="jam" class="form-select" required>
                <?php for($h=0;$h<24;$h++): ?>
                  <option value="<?=$h?>" <?=in_array($h,$booked)?'disabled':''?>><?=sprintf('%02d:00',$h)?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-6 form-floating">
              <input type="number" name="durasi" class="form-control" value="1" min="1" max="6" placeholder="Durasi" required>
              <label>Durasi (jam)</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Upload Bukti Pembayaran</label>
            <input type="file" name="bukti" class="form-control" accept="image/*" required>
          </div>
          <button name="submit" class="btn btn-primary w-100">Booking</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
