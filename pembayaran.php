<?php
include 'db.php'; session_start();
if(!isset($_SESSION['gmail'])) header('Location: login.php');
extract($_POST);
if(!isset( $lapangan, $tanggal, $jam, $durasi, $nama, $nohp)) header('Location: booking.php');

// Hitung harga
 $prices=['Lapangan 1'=>['day'=>100000,'night'=>120000],'Lapangan 2'=>['day'=>125000,'night'=>140000]];
 $period= $jam>=18?'night':'day'; $harga= $prices[ $lapangan][ $period]; $total= $harga* $durasi;

if(isset( $_POST['bayar'])){
   $stmt= $conn->prepare(
    "INSERT INTO bookings (nama,nohp,lapangan,tanggal,jam,durasi,harga_per_jam,metode,bukti,status) VALUES(?,?,?,?,?,?,?,?,?, 'lunas')"
  );
   $stmt->bind_param('sssiissss', $nama, $nohp, $lapangan, $tanggal, $jam, $durasi, $harga, $_POST['metode'], $_FILES['bukti']['name']);
  move_uploaded_file( $_FILES['bukti']['tmp_name'], 'uploads/'. $_FILES['bukti']['name']);
   $stmt->execute();
  header('Location: booking.php'); exit;
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Pembayaran</title></head><body class="bg-light">
<div class="container py-5"><div class="card mx-auto" style="max-width:500px"><div class="card-body">
<h4 class="text-center text-danger mb-4">Pembayaran</h4>
<p><strong>Nama:</strong> <?=htmlspecialchars( $nama)?></p>
<p><strong>No. Telepon:</strong> <?=htmlspecialchars( $nohp)?></p>
<p><strong>Lapangan:</strong> <?= $lapangan?> | <strong>Tanggal:</strong> <?= $tanggal?></p>
<p><strong>Jam:</strong> <?=sprintf('%02d:00', $jam)?> x <?= $durasi?> jam</p>
<p><strong>Total:</strong> Rp<?=number_format( $total,0,',','.')?></p>
<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="lapangan" value="<?= $lapangan?>">
  <input type="hidden" name="tanggal" value="<?= $tanggal?>">
  <input type="hidden" name="jam" value="<?= $jam?>">
  <input type="hidden" name="durasi" value="<?= $durasi?>">
  <input type="hidden" name="nama" value="<?=htmlspecialchars( $nama)?>">
  <input type="hidden" name="nohp" value="<?=htmlspecialchars( $nohp)?>">
  <div class="mb-3">
    <label class="form-label">Metode Pembayaran</label>
    <select name="metode" class="form-select" required>
      <option value="BCA">BCA - 1234567890</option>
      <option value="BRI">BRI - 0987654321</option>
      <option value="BNI">BNI - 1122334455</option>
      <option value="Dana">Dana - 089604224173</option>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label">Upload Bukti Pembayaran</label>
    <input type="file" name="bukti" class="form-control" accept="image/*" required>
  </div>
  <button name="bayar" class="btn btn-primary w-100">Bayar & Selesai</button>
</form>
</div></div></div>
</body></html>
