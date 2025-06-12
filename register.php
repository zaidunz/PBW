<?php
include 'db.php';
if (
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    $nama = $_POST['nama'];
    $umur = $_POST['umur'];
    $gmail = $_POST['gmail'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (nama, umur, gmail, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('siss', $nama, $umur, $gmail, $password);
    if ($stmt->execute()) {
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="assets/logo.png" alt="Logo" width="40" height="40">
      Booking Futsal
    </a>
  </div>
</nav>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title mb-4 text-center">Daftar</h3>
          <form method="POST">
            <div class="form-floating mb-3">
              <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" required>
              <label for="nama">Nama</label>
            </div>
            <div class="form-floating mb-3">
              <input type="number" class="form-control" id="umur" name="umur" placeholder="Umur" required>
              <label for="umur">Umur</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="gmail" name="gmail" placeholder="name@example.com" required>
              <label for="gmail">Gmail</label>
            </div>
            <div class="form-floating mb-4">
              <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
              <label for="password">Password</label>
            </div>
            <button class="btn btn-primary w-100" type="submit">Register</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>