<?php
include 'config.php';

if (isset($_SESSION['username']) || isset($_SESSION['admin_nim'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

$error = "";
$success = "";

// Data password admin berdasarkan 2 digit belakang NIM
$admin_passwords = [
    '2405010014' => 'TRPL14',
    '2405010032' => 'TRPL32',
    '2405010002' => 'TRPL02',
    '2405010026' => 'TRPL26',
    '2405010035' => 'TRPL35',
    '2405010012' => 'TRPL12',
    '2405010036' => 'TRPL36',
    '2405010018' => 'TRPL18',
    '2405010004' => 'TRPL04',
    '2405010048' => 'TRPL48',
    '2405010016' => 'TRPL16',
    '2405010006' => 'TRPL06',
    '2405010038' => 'TRPL38',
    '2405010040' => 'TRPL40',
    '2405010024' => 'TRPL24',
    '2405010050' => 'TRPL50',
    '2405010020' => 'TRPL20',
    '2405010055' => 'TRPL55'
];

// PROSES LOGIN ADMIN TRPL
if (isset($_POST['login_admin'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $password = $_POST['password'];
    
    // Cek apakah NIM dan nama lengkap sesuai dengan data admin
    $result = mysqli_query($conn, "SELECT * FROM admin_trpl WHERE nim='$nim' AND nama_lengkap='$nama_lengkap'");
    
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Verifikasi password (password = TRPL + 2 digit belakang NIM)
        $expected_password = 'TRPL' . substr($nim, -2);
        
        if ($password === $expected_password) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            $_SESSION['admin_nim'] = $admin['nim'];
            $_SESSION['admin_prodi'] = $admin['prodi'];
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Nama lengkap atau NIM tidak sesuai!";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TRPL 2024</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .auth-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 450px;
        }
        
        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            text-align: center;
        }
        
        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .alert {
            border-radius: 5px;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        .alert-info {
            background-color: #e8f4fd;
            border-color: #b8daff;
            color: #004085;
        }
        
        .btn {
            padding: 12px 20px;
            border-radius: 5px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .tab-pane {
            padding-top: 20px;
        }
        
        @media (max-width: 576px) {
            .auth-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2 class="mb-4">TRPL ADMIN 2024</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- LOGIN ADMIN TRPL -->
        <div class="tab-pane fade show active" id="login-admin" role="tabpanel">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap:</label>
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">NIM:</label>
                    <input type="text" name="nim" class="form-control" placeholder="Masukkan NIM" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                
                <button type="submit" name="login_admin" class="btn btn-success">Login sebagai Admin TRPL</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>