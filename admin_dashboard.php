<?php
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Buat folder uploads jika belum ada
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Tentukan section yang aktif
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// CRUD Operations
if (isset($_POST['tambah_mahasiswa'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $motivasi = mysqli_real_escape_string($conn, $_POST['motivasi']);
    $makanan_favorit = mysqli_real_escape_string($conn, $_POST['makanan_favorit']);
    $hobi = mysqli_real_escape_string($conn, $_POST['hobi']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    
    // Handle file upload
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            $gambar = uniqid() . '_' . $_FILES['gambar']['name'];
            move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar);
        } else {
            $error = "Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan!";
        }
    }
    
    if (empty($error)) {
        $insert = mysqli_query($conn, "INSERT INTO mahasiswa (nama, kota, tempat_lahir, motivasi, makanan_favorit, hobi, provinsi, gambar) 
                                       VALUES ('$nama', '$kota', '$tempat_lahir', '$motivasi', '$makanan_favorit', '$hobi', '$provinsi', '$gambar')");
        
        if ($insert) {
            $success = "Data mahasiswa berhasil ditambahkan!";
            $section = 'data-mahasiswa';
        } else {
            $error = "Gagal menambahkan data mahasiswa!";
        }
    }
}

if (isset($_POST['edit_mahasiswa'])) {
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $motivasi = mysqli_real_escape_string($conn, $_POST['motivasi']);
    $makanan_favorit = mysqli_real_escape_string($conn, $_POST['makanan_favorit']);
    $hobi = mysqli_real_escape_string($conn, $_POST['hobi']);
    $provinsi = mysqli_real_escape_string($conn, $_POST['provinsi']);
    
    // Handle file upload for edit
    $gambar = $_POST['current_gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Delete old image if exists
            if (!empty($gambar) && file_exists('uploads/' . $gambar)) {
                unlink('uploads/' . $gambar);
            }
            
            $gambar = uniqid() . '_' . $_FILES['gambar']['name'];
            move_uploaded_file($_FILES['gambar']['tmp_name'], 'uploads/' . $gambar);
        } else {
            $error = "Hanya file JPG, JPEG, PNG, dan GIF yang diizinkan!";
        }
    }
    
    if (empty($error)) {
        $update = mysqli_query($conn, "UPDATE mahasiswa SET nama='$nama', kota='$kota', tempat_lahir='$tempat_lahir', 
                                      motivasi='$motivasi', makanan_favorit='$makanan_favorit', hobi='$hobi', 
                                      provinsi='$provinsi', gambar='$gambar' WHERE id='$id'");
        
        if ($update) {
            $success = "Data mahasiswa berhasil diupdate!";
            $section = 'data-mahasiswa';
        } else {
            $error = "Gagal mengupdate data mahasiswa!";
        }
    }
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Get image filename to delete
    $result = mysqli_query($conn, "SELECT gambar FROM mahasiswa WHERE id='$id'");
    $row = mysqli_fetch_assoc($result);
    
    // Delete image file
    if (!empty($row['gambar']) && file_exists('uploads/' . $row['gambar'])) {
        unlink('uploads/' . $row['gambar']);
    }
    
    $delete = mysqli_query($conn, "DELETE FROM mahasiswa WHERE id='$id'");
    
    if ($delete) {
        $success = "Data mahasiswa berhasil dihapus!";
    } else {
        $error = "Gagal menghapus data mahasiswa!";
    }
}

// Get all mahasiswa
$mahasiswa = mysqli_query($conn, "SELECT * FROM mahasiswa ORDER BY nama");
$total_mahasiswa = mysqli_num_rows($mahasiswa);

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM mahasiswa WHERE id='$edit_id'");
    $edit_data = mysqli_fetch_assoc($result);
    $section = 'edit-data';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TRPL 2024</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
                <div class="position-sticky">
                    <div class="sidebar-logo text-center py-4">
                        <h4 class="text-white">TRPL Admin</h4>
                        <small class="text-white-50"><?php echo $_SESSION['admin_nama']; ?></small><br>
                        <small class="text-white-50">NIM: <?php echo $_SESSION['admin_nim']; ?></small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $section == 'dashboard' ? 'active' : ''; ?>" href="?section=dashboard">
                               🏠︎ Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $section == 'data-mahasiswa' ? 'active' : ''; ?>" href="?section=data-mahasiswa">
                               👤 Data Mahasiswa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $section == 'tambah-data' ? 'active' : ''; ?>" href="?section=tambah-data">
                               👥 Tambah Mahasiswa
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Admin Dashboard TRPL</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="me-3">Halo, <?php echo $_SESSION['admin_nama']; ?> (<?php echo $_SESSION['admin_nim']; ?>)</span>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Dashboard Content -->
                <?php if ($section == 'dashboard'): ?>
                <div id="dashboard" class="content-section">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-white bg-primary mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Total Mahasiswa</h5>
                                    <h2 class="card-text"><?php echo $total_mahasiswa; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Admin</h5>
                                    <h2 class="card-text"><?php echo $_SESSION['admin_nama']; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-warning mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Prodi</h5>
                                    <h2 class="card-text">TRPL</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <a href="?section=tambah-data" class="btn btn-primary me-2">Tambah Mahasiswa</a>
                                    <a href="?section=data-mahasiswa" class="btn btn-success">Lihat Data Mahasiswa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Data Mahasiswa Content -->
                <?php if ($section == 'data-mahasiswa'): ?>
                <div id="data-mahasiswa" class="content-section">
                    <h3>👤 Data Mahasiswa TRPL 2024</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>Nama</th>
                                    <th>Kota</th>
                                    <th>Motivasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                mysqli_data_seek($mahasiswa, 0);
                                while ($row = mysqli_fetch_assoc($mahasiswa)): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td>
                                        <?php if (!empty($row['gambar'])): ?>
                                            <img src="uploads/<?php echo $row['gambar']; ?>" alt="<?php echo $row['nama']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                <span>📷</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $row['nama']; ?></td>
                                    <td><?php echo $row['kota']; ?></td>
                                    <td><?php echo substr($row['motivasi'], 0, 50) . '...'; ?></td>
                                    <td>
                                        <a href="?section=edit-data&edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="?section=data-mahasiswa&hapus=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data <?php echo $row['nama']; ?>?')">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tambah Data Content -->
                <?php if ($section == 'tambah-data'): ?>
                <div id="tambah-data" class="content-section">
                    <h3>👥 Tambah Data Mahasiswa Baru</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap:</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kota:</label>
                                    <input type="text" name="kota" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tempat, Tanggal Lahir:</label>
                                    <input type="text" name="tempat_lahir" class="form-control" placeholder="Contoh: Medan, 1 Januari 2000" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Provinsi:</label>
                                    <input type="text" name="provinsi" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Motivasi:</label>
                                    <textarea name="motivasi" class="form-control" rows="3" placeholder="Tuliskan motivasi hidup..." required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Makanan Favorit:</label>
                                    <input type="text" name="makanan_favorit" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hobi (pisahkan dengan koma):</label>
                                    <input type="text" name="hobi" class="form-control" placeholder="Contoh: Membaca, Olahraga, Musik" required>
                                </div>
                                
                                <!-- File Upload Area -->
                                <div class="mb-3">
                                    <label class="form-label">Upload Foto:</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: JPG, JPEG, PNG, GIF (Maksimal 2MB)</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="tambah_mahasiswa" class="btn btn-primary">💾 Simpan Data Mahasiswa</button>
                        <a href="?section=data-mahasiswa" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Edit Data Content -->
                <?php if ($section == 'edit-data' && $edit_data): ?>
                <div id="edit-data" class="content-section">
                    <h3>👤✏️ Edit Data Mahasiswa</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <input type="hidden" name="current_gambar" value="<?php echo $edit_data['gambar']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap:</label>
                                    <input type="text" name="nama" class="form-control" value="<?php echo $edit_data['nama']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kota:</label>
                                    <input type="text" name="kota" class="form-control" value="<?php echo $edit_data['kota']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tempat, Tanggal Lahir:</label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="<?php echo $edit_data['tempat_lahir']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Provinsi:</label>
                                    <input type="text" name="provinsi" class="form-control" value="<?php echo $edit_data['provinsi']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Motivasi:</label>
                                    <textarea name="motivasi" class="form-control" rows="3" required><?php echo $edit_data['motivasi']; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Makanan Favorit:</label>
                                    <input type="text" name="makanan_favorit" class="form-control" value="<?php echo $edit_data['makanan_favorit']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hobi:</label>
                                    <input type="text" name="hobi" class="form-control" value="<?php echo $edit_data['hobi']; ?>" required>
                                </div>
                                
                                <!-- File Upload untuk Edit -->
                                <div class="mb-3">
                                    <label class="form-label">Upload Foto Baru (opsional):</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: JPG, JPEG, PNG, GIF (Maksimal 2MB)</small>
                                    <?php if (!empty($edit_data['gambar'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Foto saat ini: <?php echo $edit_data['gambar']; ?></small><br>
                                            <img src="uploads/<?php echo $edit_data['gambar']; ?>" alt="Current Photo" style="max-width: 100px; margin-top: 5px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="edit_mahasiswa" class="btn btn-primary">💾 Update Data</button>
                        <a href="?section=data-mahasiswa" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>