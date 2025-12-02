<?php
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// Get student data from database
$result = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id='$id'");
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Data mahasiswa tidak ditemukan!");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $student['nama'] ?> - TRPL 2024</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <header class="header">
        <div class="container position-relative">
            <!-- Tombol Logout di kanan -->
            <a href="logout.php" 
               class="btn btn-danger position-absolute" 
               style="top: 20px; right: 20px;">
               Logout
            </a>

            <div class="text-center">
                <h1 class="display-4 fw-bold"><?= $student['nama'] ?></h1>
                <p class="lead">Mahasiswa TRPL 2024</p>
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-4 text-center">
                <?php if (!empty($student['gambar'])): ?>
                    <img src="uploads/<?= $student['gambar'] ?>" alt="<?= $student['nama'] ?>" class="img-fluid rounded-circle profile-img mb-3">
                <?php else: ?>
                    <div class="no-image-profile">📷</div>
                <?php endif; ?>
                <h3><?= $student['nama'] ?></h3>
                <h5><?= $student['kota'] ?></h5>
            </div>
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Biodata</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Tempat, Tanggal Lahir:</strong> <?= $student['tempat_lahir'] ?></p>
                        <p><strong>Provinsi:</strong> <?= $student['provinsi'] ?></p>
                        <p>Mahasiswa aktif Teknologi Rekayasa Perangkat Lunak angkatan 2024.</p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Motivasi</h3>
                    </div>
                    <div class="card-body">
                        <blockquote class="blockquote">
                            <p>"<?= $student['motivasi'] ?>"</p>
                        </blockquote>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Makanan Favorit</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item"><?= $student['makanan_favorit'] ?></li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Hobi</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php 
                            $hobbies = explode(',', $student['hobi']);
                            foreach ($hobbies as $hobi): 
                            ?>
                                <li class="list-group-item"><?= trim($hobi) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Kembali ke Halaman Utama</a>
        </div>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2024 Mahasiswa TRPL. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>