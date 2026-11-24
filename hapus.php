<?php
session_start();
include "koneksi.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM laporan_kerusakan WHERE id_laporan = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['pesan'] = "Berhasil! Data laporan telah dihapus.";
        $_SESSION['tipe']  = "sukses";
    } catch (PDOException $e) {
        $_SESSION['pesan'] = "Gagal menghapus data: " . $e->getMessage();
        $_SESSION['tipe']  = "error";
    }
}

header("Location: index.php");
exit();
?>
