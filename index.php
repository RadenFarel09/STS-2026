<?php
session_start();
include "koneksi.php";

// PROSES CREATE (Tambah Data)
if (isset($_POST['kirim'])) {
    $nama_pelapor   = trim($_POST['nama_pelapor']);
    $nama_fasilitas = trim($_POST['nama_fasilitas']);
    $lokasi         = trim($_POST['lokasi']);
    $id_kategori    = $_POST['id_kategori'];
    $deskripsi      = trim($_POST['deskripsi']);
    $prioritas      = $_POST['prioritas'];

    // Validasi Server-Side (Wajib di Juknis)
    if (empty($nama_pelapor) || empty($nama_fasilitas) || empty($lokasi) || empty($deskripsi)) {
        $_SESSION['pesan'] = "Gagal! Semua field wajib diisi dan tidak boleh hanya spasi.";
        $_SESSION['tipe']  = "error";
    } else {
        try {
            $sql = "INSERT INTO laporan_kerusakan (nama_pelapor, nama_fasilitas, lokasi, id_kategori, deskripsi, prioritas) 
                    VALUES (:nama_pelapor, :nama_fasilitas, :lokasi, :id_kategori, :deskripsi, :prioritas)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':nama_pelapor'   => $nama_pelapor,
                ':nama_fasilitas' => $nama_fasilitas,
                ':lokasi'         => $lokasi,
                ':id_kategori'    => $id_kategori,
                ':deskripsi'      => $deskripsi,
                ':prioritas'      => $prioritas
            ]);

            $_SESSION['pesan'] = "Berhasil! Laporan kerusakan telah ditambahkan.";
            $_SESSION['tipe']  = "sukses";
        } catch (PDOException $e) {
            $_SESSION['pesan'] = "Terjadi kesalahan sistem: " . $e->getMessage();
            $_SESSION['tipe']  = "error";
        }
    }
    header("Location: index.php");
    exit();
}

// PROSES READ (Ambil Data dengan JOIN)
$query = $conn->query("SELECT lr.*, kf.nama_kategori FROM laporan_kerusakan lr 
                       JOIN kategori_fasilitas kf ON lr.id_kategori = kf.id_kategori 
                       ORDER BY lr.id_laporan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lapor Kerusakan Fasilitas Sekolah</title>
    <link href="sts.css" rel="stylesheet">
</head>
<body>

    <h2>Sistem Pelaporan Kerusakan Fasilitas Sekolah</h2>

    <!-- MENAMPILKAN PESAN NOTIFIKASI -->
    <?php if (isset($_SESSION['pesan'])): ?>
        <div class="alert <?php echo $_SESSION['tipe']; ?>">
            <?php 
                echo $_SESSION['pesan']; 
                unset($_SESSION['pesan']);
                unset($_SESSION['tipe']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="baris">
        <div class="kiri kotak">
            <h3>Buat Laporan Baru</h3>
            <form action="" method="POST">
                <label>Nama Pelapor</label>
                <input type="text" name="nama_pelapor" required>
                
                <label>Nama Fasilitas</label>
                <input type="text" name="nama_fasilitas" required>
                
                <label>Lokasi</label>
                <input type="text" name="lokasi" required>
                
                <label>Kategori</label>
                <select name="id_kategori" required>
                    <option value="1">Elektronik</option>
                    <option value="2">Furnitur</option>
                    <option value="3">Jaringan</option>
                    <option value="4">Peralatan Kelas</option>
                </select>
                
                <label>Deskripsi Kerusakan</label>
                <textarea name="deskripsi" rows="3" required></textarea>
                
                <label>Prioritas</label>
                <select name="prioritas" required>
                    <option value="Rendah">Rendah</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Tinggi">Tinggi</option>
                </select>
                
                <button type="submit" name="kirim">Kirim Laporan</button>
            </form>
        </div>

        <div class="kanan kotak">
            <h3>Daftar Laporan Kerusakan</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Fasilitas</th>
                        <th>Lokasi</th>
                        <th>Pelapor</th>
                        <th>Deskripsi</th>
                        <th>Prioritas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $no = 1;
                    while ($data = $query->fetch(PDO::FETCH_ASSOC)) { 
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td>
                            <b><?php echo htmlspecialchars($data['nama_fasilitas']); ?></b><br>
                            <small style="color: gray;"><?php echo htmlspecialchars($data['nama_kategori']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($data['lokasi']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_pelapor']); ?></td>
                        <td><?php echo htmlspecialchars($data['deskripsi']); ?></td>
                        <td><span class="badge <?php echo strtolower($data['prioritas']); ?>"><?php echo $data['prioritas']; ?></span></td>
                        <td>
                            <a href="edit.php?id=<?php echo $data['id_laporan']; ?>" class="btn-edit">Edit</a>
                            <a href="hapus.php?id=<?php echo $data['id_laporan']; ?>" class="btn-hapus" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
