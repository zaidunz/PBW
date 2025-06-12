<?php
include 'db.php'; session_start();
if(!isset($_SESSION['gmail'])) header('Location: login.php');

// Harga per lapangan
define('PRICES', ['Lapangan 1'=>['day'=>100000,'night'=>120000],'Lapangan 2'=>['day'=>125000,'night'=>140000]]);

$lap = $_POST['lapangan'] ?? '';
$tgl = $_POST['tanggal'] ?? '';
$booked = [];
if(isset($_POST['check'])){
  $res = $conn->query("SELECT jam, durasi, nama FROM bookings WHERE lapangan='{$lap}' AND tanggal='{$tgl}' AND status='lunas'");
  while($r = $res->fetch_assoc()){
    for($i=0; $i<$r['durasi']; $i++){
      $hour = $r['jam'] + $i;
      $booked[$hour] = $r['nama'];
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Pilih Slot - Booking Futsal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark" style="background:#dc3545">
  <div class="container">
    <a class="navbar-brand" href="index.php">Booking Futsal</a>
  </div>
</nav>
<div class="container py-5">
  <div class="card mx-auto" style="max-width:600px">
    <div class="card-body">
      <h4 class="text-center text-danger mb-4">Pilih Slot</h4>
      <form method="POST" class="mb-4">
        <div class="mb-3">
          <select name="lapangan" class="form-select" required>
            <option value="">-- Pilih Lapangan --</option>
            <option value="Lapangan 1" <?=  $lap=='Lapangan 1'?'selected':'' ?>>Lapangan 1</option>
            <option value="Lapangan 2" <?=  $lap=='Lapangan 2'?'selected':'' ?>>Lapangan 2</option>
          </select>
        </div>
        <div class="mb-3">
          <input type="date" name="tanggal" class="form-control" value="<?=  $tgl ?>" required>
        </div>
        <button type="submit" name="check" class="btn btn-secondary w-100">Cek Jadwal</button>
      </form>

      <?php if( $lap &&  $tgl): ?>
        <p><strong>Jam terisi:</strong>
          <?php if(!empty( $booked)){
            foreach( $booked as  $h =>  $u){
              echo sprintf('%02d:00 (%s) ',  $h, htmlspecialchars( $u));
            }
          } else echo 'Tidak ada'; ?>
        </p>

        <form action="pembayaran.php" method="POST">
          <input type="hidden" name="lapangan" value="<?=  $lap ?>">
          <input type="hidden" name="tanggal" value="<?=  $tgl ?>">
          <div class="mb-3">
            <select name="jam" class="form-select" required>
              <?php for( $h=0;  $h<24;  $h++): ?>
                <option value="<?=  $h ?>" <?= isset( $booked[ $h])?'disabled':'' ?>><?= sprintf('%02d:00',  $h) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="mb-3">
            <input type="number" name="durasi" class="form-control" placeholder="Durasi (jam)" min="1" max="6" required>
          </div>
          <div class="mb-3">
            <input type="text" name="nama" class="form-control" placeholder="Nama Penyewa" required>
          </div>
          <div class="mb-3">
            <input type="text" name="nohp" class="form-control" placeholder="No. Telepon" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Lanjut Pembayaran</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html></html>
