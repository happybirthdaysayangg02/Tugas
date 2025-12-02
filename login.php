<?php
include 'config.php';

if (isset($_SESSION['username'])) {
    header("Location: user_dashboard.php");
    exit;
}

$error = "";
$success = "";

// PROSES REGISTER USER BIASA
if (isset($_POST['register'])) {
    $nama_depan = mysqli_real_escape_string($conn, $_POST['nama_depan']);
    $nama_belakang = mysqli_real_escape_string($conn, $_POST['nama_belakang']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    // Cek username 
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah ada!";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user
        $insert = mysqli_query($conn, "INSERT INTO users (nama_depan, nama_belakang, username, password) 
                                        VALUES ('$nama_depan', '$nama_belakang', '$username', '$hashed_password')");
        
        if ($insert) {
            $success = "Registrasi berhasil! Silakan login.";
        } else {
            $error = "Registrasi gagal!";
        }
    }
}

// PROSES LOGIN USER BIASA
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['login_username']);
    $password = $_POST['login_password'];
    
    // Cari user
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['nama_depan'] = $user['nama_depan'];
            $_SESSION['nama_belakang'] = $user['nama_belakang'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'user';
            header("Location: user_dashboard.php");
            exit;
        } else {
            $error = "Username/password salah!";
        }
    } else {
        $error = "Username/password salah!";
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
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="auth-container">
        <h2 class="text-center mb-4">TRPL 2024</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs nav-justified mb-4" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-user-tab" data-bs-toggle="tab" data-bs-target="#login-user" type="button" role="tab">Login User</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Daftar User</button>
            </li>
        </ul>

        <div class="tab-content" id="authTabContent">
            <!-- LOGIN USER BIASA -->
            <div class="tab-pane fade show active" id="login-user" role="tabpanel">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username:</label>
                        <input type="text" name="login_username" class="form-control" placeholder="Masukkan username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password:</label>
                        <input type="password" name="login_password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    
                    <button type="submit" name="login_user" class="btn btn-primary">Login sebagai User</button>
                </form>
            </div>

            <!-- REGISTER USER BIASA -->
            <div class="tab-pane fade" id="register" role="tabpanel">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Depan:</label>
                            <input type="text" name="nama_depan" class="form-control" placeholder="Masukkan nama depan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Belakang:</label>
                            <input type="text" name="nama_belakang" class="form-control" placeholder="Masukkan nama belakang" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username:</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password:</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    
                    <button type="submit" name="register" class="btn btn-warning">Daftar sebagai User</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>