<?php
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Get mahasiswa data from database
$result = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY nama");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahasiswa TRPL 2024</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <header class="header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="text-center w-100">
                <h1 class="display-5 fw-bold">Mahasiswa/Mahasiswi TRPL 2024</h1>
                <p class="lead">Teknologi Rekayasa Perangkat Lunak - Angkatan 2024</p>
            </div>

            <!-- Tombol Logout -->
            <a href="logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>
    </header>

    <main class="container my-5">
        <div class="row">
            <?php while ($student = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-img-container">
                            <?php if (!empty($student['gambar'])): ?>
                                <img 
                                    src="uploads/<?= $student['gambar'] ?>" 
                                    class="card-img-top" 
                                    alt="<?= $student['nama'] ?>"
                                >
                            <?php else: ?>
                                <div class="no-image">📷</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= $student['nama'] ?></h5>
                            <p class="card-text"><?= $student['kota'] ?></p>
                            <p class="card-text motivasi">"<?= $student['motivasi'] ?>"</p>
                            <a href="profile.php?id=<?= $student['id'] ?>" class="btn btn-primary mt-auto">See Profile</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0"> © 2024 Mahasiswa TRPL. Semua Hak Dilindungi.</p>
            <p class="mb-0">Teknologi Rekayasa Perangkat Lunak - Angkatan 2024</p>
        </div>
    </footer>
</body>
</html>