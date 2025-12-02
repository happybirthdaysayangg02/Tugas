<?php
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$nama_lengkap = $_SESSION['nama_depan'] . ' ' . $_SESSION['nama_belakang'];
$username = $_SESSION['username'];

// Get mahasiswa data from database
$mahasiswa = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY nama");
$total_mahasiswa = mysqli_num_rows($mahasiswa);

// date functions 
$tanggal_lengkap = date("l, d F Y");
$waktu_sekarang = date("H:i:s");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - TRPL 2024</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user_dashboard.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="welcome-header text-center">
            <h1>Selamat Datang!</h1>
            <p>Halo, <strong><?php echo $nama_lengkap; ?></strong></p>
            <p>Username: <strong><?php echo $username; ?></strong></p>
        </div>

        <!-- Date and Time -->
        <div class="dashboard-card text-center">
            <h3><?php echo $tanggal_lengkap; ?></h3>
            <h2 id="liveClock"><?php echo $waktu_sekarang; ?></h2>
            <p>Waktu Server</p>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <div class="col-md-4">
                <div class="dashboard-card text-center">
                    <h3><?php echo $total_mahasiswa; ?></h3>
                    <p>Total Mahasiswa</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card text-center">
                    <h3>TRPL</h3>
                    <p>Program Studi</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card text-center">
                    <h3>2024</h3>
                    <p>Angkatan</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="dashboard-card text-center">
            <h4>Akses Cepat</h4>
            <a href="index.php" class="btn-custom me-2">Lihat Gallery Mahasiswa</a>
            <a href="logout.php" class="btn-custom btn-logout">Logout</a>
        </div>
    </div>

    <!-- JavaScript Clock -->
    <script>
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var seconds = now.getSeconds();
            
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            var timeString = hours + ":" + minutes + ":" + seconds;
            document.getElementById("liveClock").innerHTML = timeString;
        }

        setInterval(updateClock, 1000);
    </script>
</body>
</html>