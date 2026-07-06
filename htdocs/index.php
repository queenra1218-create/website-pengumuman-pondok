<?php
session_start();
require_once 'config.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error = '';
$success = '';

// LOGIN
if (isset($_POST['login'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF token tidak valid');
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        if (password_verify($password, $data['password'])) {
            session_regenerate_id(true);
            $_SESSION['login'] = true;
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $data['role'];
            header("Location: index.php");
            exit();
        }
    }

    $error = "Username atau password salah!";
    mysqli_stmt_close($stmt);
}

// LOGOUT
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit();
}

// TAMBAH PENGUMUMAN
if (isset($_POST['tambah']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF token tidak valid');
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO pengumuman (judul, isi, tanggal, kategori) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $_POST['judul'], $_POST['isi'], $_POST['tanggal'], $_POST['kategori']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $success = "Pengumuman berhasil ditambahkan!";
}

// EDIT PENGUMUMAN
if (isset($_POST['edit']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('CSRF token tidak valid');
    }

    $stmt = mysqli_prepare($conn, "UPDATE pengumuman SET judul=?, isi=?, tanggal=?, kategori=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $_POST['judul'], $_POST['isi'], $_POST['tanggal'], $_POST['kategori'], $_POST['id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $success = "Pengumuman berhasil diperbarui!";
    header("Location: index.php");
    exit();
}

// HAPUS PENGUMUMAN
if (isset($_GET['hapus']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $stmt = mysqli_prepare($conn, "DELETE FROM pengumuman WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $_GET['hapus']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: index.php");
    exit();
}

// AMBIL DATA PENGUMUMAN
$pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY tanggal DESC");

// DATA EDIT
$edit_data = null;
if (isset($_GET['edit']) && isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM pengumuman WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $_GET['edit']);
    mysqli_stmt_execute($stmt);
    $query_edit = mysqli_stmt_get_result($stmt);
    $edit_data = mysqli_fetch_assoc($query_edit);
    mysqli_stmt_close($stmt);
}

$kategori_list = ['Umum', 'Akademik', 'Kegiatan', 'Keamanan'];
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
$bg_gradient = $is_admin ? 'linear-gradient(135deg, #667eea, #764ba2)' : 'linear-gradient(135deg, #11998e, #38ef7d)';
$badge_color = $is_admin ? '#667eea' : '#11998e';
$role_label = $is_admin ? 'ADMIN' : 'WALI';
$dashboard_title = $is_admin ? 'Admin' : 'Wali Santri';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: <?= $bg_gradient ?>;
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        h1, h2 { color: #333; margin-bottom: 15px; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .role-badge {
            background: <?= $badge_color ?>;
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        textarea { min-height: 100px; resize: vertical; }
        button, .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px 0;
            font-size: 14px;
        }
        .btn-primary { background: #667eea; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background: #f0f2f5; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
        .kategori-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .kategori-Akademik { background: #3498db; color: white; }
        .kategori-Kegiatan { background: #2ecc71; color: white; }
        .kategori-Keamanan { background: #e74c3c; color: white; }
        .kategori-Umum { background: #95a5a6; color: white; }
        .login-box { max-width: 400px; margin: 50px auto; }
        .text-center { text-align: center; }
        .flex { display: flex; gap: 10px; }
        .mt-15 { margin-top: 15px; }
        .py-40 { padding: 40px 0; }
        .text-muted { color: #666; }
        .overflow-x { overflow-x: auto; }
        @media (max-width: 768px) {
            .card { padding: 15px; }
            th, td { font-size: 12px; }
            .header { flex-direction: column; text-align: center; gap: 15px; }
        }
    </style>
</head>
<body>
<div class="container">

<?php if (!isset($_SESSION['login'])): ?>

<div class="card login-box">
    <div class="text-center" style="font-size:50px;">🕌</div>
    <h1 class="text-center">Pondok Pesantren</h1>
    <p class="text-center text-muted" style="margin-bottom:20px;">Sistem Informasi Digital</p>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login" class="btn-primary" style="width:100%;">Login</button>
    </form>
    <div class="success mt-15 text-center">
        <strong>Demo Akun:</strong><br>
        Admin: admin / password<br>
        Wali: wali / password
    </div>
</div>

<?php else: ?>

<div class="card">
    <div class="header">
        <div>
            <h1>Dashboard <?= $dashboard_title ?></h1>
            <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></p>
        </div>
        <div class="text-center">
            <span class="role-badge"><?= $role_label ?></span><br>
            <a href="?logout=1" class="btn btn-danger btn-sm mt-15" style="display:inline-block;">Logout</a>
        </div>
    </div>
</div>

<?php if ($success): ?>
    <div class="card success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($is_admin): ?>
<div class="card">
    <h2>Tambah Pengumuman</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="text" name="judul" placeholder="Judul Pengumuman" required>
        <textarea name="isi" placeholder="Isi Pengumuman" required></textarea>
        <div class="flex">
            <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            <select name="kategori" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?= htmlspecialchars($kat) ?>"><?= htmlspecialchars($kat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="tambah" class="btn-success">Simpan</button>
    </form>
</div>
<?php endif; ?>

<?php if ($is_admin && $edit_data): ?>
<div class="card">
    <h2>Edit Pengumuman</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <input type="hidden" name="id" value="<?= intval($edit_data['id']) ?>">
        <input type="text" name="judul" value="<?= htmlspecialchars($edit_data['judul']) ?>" required>
        <textarea name="isi" required><?= htmlspecialchars($edit_data['isi']) ?></textarea>
        <div class="flex">
            <input type="date" name="tanggal" value="<?= htmlspecialchars($edit_data['tanggal']) ?>" required>
            <select name="kategori" required>
                <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?= htmlspecialchars($kat) ?>" <?= $edit_data['kategori'] == $kat ? 'selected' : '' ?>><?= htmlspecialchars($kat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="edit" class="btn-warning">Update</button>
        <a href="index.php" class="btn btn-danger btn-sm">Batal</a>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <h2>Daftar Pengumuman</h2>
    <?php if (mysqli_num_rows($pengumuman) > 0): ?>
        <div class="overflow-x">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Judul</th>
                        <th>Isi</th>
                        <th>Tanggal</th>
                        <?php if ($is_admin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($pengumuman)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <span class="kategori-badge kategori-<?= htmlspecialchars($row['kategori']) ?>">
                                <?= htmlspecialchars($row['kategori']) ?>
                            </span>
                        </td>
                        <td><strong><?= htmlspecialchars($row['judul']) ?></strong></td>
                        <td><?= nl2br(htmlspecialchars($row['isi'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <?php if ($is_admin): ?>
                            <td>
                                <a href="?edit=<?= intval($row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?hapus=<?= intval($row['id']) ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center text-muted py-40">Belum ada pengumuman.</p>
    <?php endif; ?>
</div>

<?php if ($_SESSION['role'] == 'wali'): ?>
<div class="card">
    <div style="background:#d1ecf1; padding:15px; border-radius:8px;">
        <strong>Info:</strong> Anda login sebagai Wali Santri. Hanya bisa melihat pengumuman.
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

</div>
</body>
</html>
