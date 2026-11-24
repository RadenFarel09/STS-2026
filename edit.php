<?php
session_start();
include "koneksi.php";

// Pastikan ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Ambil data lama berdasarkan ID untuk ditampilkan di form
$stmt = $conn->prepare("SELECT * FROM laporan_kerusakan WHERE id_laporan = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    $_SESSION['pesan'] = "Data laporan tidak ditemukan!";
    $_SESSION['tipe']  = "error";
    header("Location: index.php");
    exit();
}

// PROSES UPDATE DATA
if (isset($_POST['update'])) {
    $nama_pelapor   = trim($_POST['nama_pelapor']);
    $nama_fasilitas = trim($_POST['nama_fasilitas']);
    $lokasi         = trim($_POST['lokasi']);
    $id_kategori    = $_POST['id_kategori'];
    $deskripsi      = trim($_POST['deskripsi']);
    $prioritas      = $_POST['prioritas'];

    if (empty($nama_pelapor) || empty($nama_fasilitas) || empty($lokasi) || empty($deskripsi)) {
        $_SESSION['pesan'] = "Gagal mengubah! Semua field wajib diisi.";
        $_SESSION['tipe']  = "error";
    } else {
        $sql = "UPDATE laporan_kerusakan SET 
                nama_pelapor = :nama_pelapor, 
                nama_fasilitas = :nama_fasilitas, 
                lokasi = :lokasi, 
                id_kategori = :id_kategori, 
                deskripsi = :deskripsi, 
                prioritas = :prioritas 
                WHERE id_laporan = :id";
        
        $updateStmt = $conn->prepare($sql);
        $updateStmt->execute([
            ':nama_pelapor'   => $nama_pelapor,
            ':nama_fasilitas' => $nama_fasilitas,
            ':lokasi'         => $lokasi,
            ':id_kategori'    => $id_kategori,
            ':deskripsi'      => $deskripsi,
            ':prioritas'      => $prioritas,
            ':id'             => $id
        ]);

        $_SESSION['pesan'] = "Berhasil! Data laporan telah diperbarui.";
        $_SESSION['tipe']  = "sukses";
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Laporan Kerusakan</title>
    <link href="sts.css" rel="stylesheet">
</head>
<body>

    <div class="kotak" style="max-width: 500px; margin: auto;">
        <h3>Edit Laporan Kerusakan</h3>
        <form action="" method="POST">
            <label>Nama Pelapor</label>
            <input type="text" name="nama_pelapor" value="<?php echo htmlspecialchars($data['nama_pelapor']); ?>" required>
            
            <label>Nama Fasilitas</label>
            <input type="text" name="nama_fasilitas" value="<?php echo htmlspecialchars($data['nama_fasilitas']); ?>" required>
            
            <label>Lokasi</label>
            <input type="text" name="lokasi" value="<?php echo htmlspecialchars($data['lokasi']); ?>" required>
            
            <label>Kategori</label>
            <select name="id_kategori" required>
                <option value="1" <?php if($data['id_kategori'] == 1) echo 'selected'; ?>>Elektronik</option>
                <option value="2" <?php if($data['id_kategori'] == 2) echo 'selected'; ?>>Furnitur</option>
                <option value="3" <?php if($data['id_kategori'] == 3) echo 'selected'; ?>>Jaringan</option>
                <option value="4" <?php if($data['id_kategori'] == 4) echo 'selected'; ?>>Peralatan Kelas</option>
            </select>
            
            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="3" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            
            <label>Prioritas</label>
            <select name="prioritas" required>
                <option value="Rendah" <?php if($data['prioritas'] == 'Rendah') echo 'selected'; ?>>Rendah</option>
                <option value="Sedang" <?php if($data['prioritas'] == 'Sedang') echo 'selected'; ?>>Sedang</option>
                <option value="Tinggi" <?php if($data['prioritas'] == 'Tinggi') echo 'selected'; ?>>Tinggi</option>
            </select>
            
            <button type="submit" name="update">Simpan Perubahan</button>
            <a href="index.php" class="btn-batal">Batal</a>
        </form>
    </div>

</body>
</html>
