<?php
class Pengaduan {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function semua() {
        $sql = "SELECT p.*, s.nama, s.kelas, k.nama_kategori
                FROM pengaduan p
                JOIN siswa s ON s.nis=p.nis
                JOIN kategori k ON k.id_kategori=p.id_kategori
                ORDER BY p.created_at DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function siswa($nis) {
        $nis = (int)$nis;
        $sql = "SELECT p.*, k.nama_kategori
                FROM pengaduan p
                JOIN kategori k ON k.id_kategori=p.id_kategori
                WHERE p.nis=$nis
                ORDER BY p.created_at DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function get($id) {
        $id = (int)$id;
        $sql = "SELECT p.*, s.nama, s.kelas, s.username, k.nama_kategori
                FROM pengaduan p
                JOIN siswa s ON s.nis=p.nis
                JOIN kategori k ON k.id_kategori=p.id_kategori
                WHERE p.id_pengaduan=$id";
        $q = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($q);
    }

    public function tambah($nis, $id_kategori, $lokasi, $keterangan, $foto = null) {
        $nis = (int)$nis;
        $id_kategori = (int)$id_kategori;
        $lokasi = mysqli_real_escape_string($this->conn, trim($lokasi));
        $keterangan = mysqli_real_escape_string($this->conn, trim($keterangan));
        $fotoSql = $foto ? "'" . mysqli_real_escape_string($this->conn, $foto) . "'" : "NULL";

        return mysqli_query($this->conn,
            "INSERT INTO pengaduan (nis,id_kategori,lokasi,keterangan,foto,status)
             VALUES ($nis,$id_kategori,'$lokasi','$keterangan',$fotoSql,'Menunggu')");
    }

    public function updateStatus($id, $status, $feedback, $catatan, $username) {
        $id = (int)$id;
        $status = mysqli_real_escape_string($this->conn, $status);
        $feedback = mysqli_real_escape_string($this->conn, trim($feedback));
        $catatan = mysqli_real_escape_string($this->conn, trim($catatan));
        $username = mysqli_real_escape_string($this->conn, $username);

        mysqli_begin_transaction($this->conn);

        $ok1 = mysqli_query($this->conn,
            "UPDATE pengaduan SET status='$status' WHERE id_pengaduan=$id");

        $ok2 = mysqli_query($this->conn,
            "INSERT INTO penanganan (id_pengaduan,username,status,feedback,catatan)
             VALUES ($id,'$username','$status','$feedback','$catatan')");

        if ($ok1 && $ok2) {
            mysqli_commit($this->conn);
            return true;
        }

        mysqli_rollback($this->conn);
        return false;
    }

    public function penanganan($id) {
        $id = (int)$id;
        return mysqli_query($this->conn,
            "SELECT p.*, a.nama AS nama_admin
             FROM penanganan p
             LEFT JOIN admin a ON a.username=p.username
             WHERE p.id_pengaduan=$id
             ORDER BY p.created_at DESC");
    }

    public function hapusSiswa($id, $nis) {
        $id  = (int)$id;
        $nis = (int)$nis;

        // Ambil data pengaduan milik siswa ini
        $q = mysqli_query($this->conn,
            "SELECT foto, status FROM pengaduan WHERE id_pengaduan=$id AND nis=$nis");
        $row = mysqli_fetch_assoc($q);

        // Tidak ditemukan atau bukan milik siswa ini
        if (!$row) return ['ok' => false, 'message' => 'Pengaduan tidak ditemukan.'];

        // Hanya boleh hapus kalau Menunggu atau Ditolak
        if (!in_array($row['status'], ['Menunggu', 'Ditolak'])) {
            return ['ok' => false, 'message' => 'Pengaduan yang sedang diproses atau sudah selesai tidak dapat dihapus.'];
        }

        mysqli_begin_transaction($this->conn);

        // Hapus data penanganan terkait (jaga-jaga ada FK)
        $ok1 = mysqli_query($this->conn,
            "DELETE FROM penanganan WHERE id_pengaduan=$id");

        // Hapus pengaduan
        $ok2 = mysqli_query($this->conn,
            "DELETE FROM pengaduan WHERE id_pengaduan=$id AND nis=$nis");

        if ($ok1 && $ok2 && mysqli_affected_rows($this->conn) > 0) {
            mysqli_commit($this->conn);

            // Hapus file foto jika ada
            if (!empty($row['foto'])) {
                $path = __DIR__ . '/../assets/uploads/' . $row['foto'];
                if (file_exists($path)) @unlink($path);
            }

            return ['ok' => true, 'message' => 'Pengaduan berhasil dihapus.'];
        }

        mysqli_rollback($this->conn);
        return ['ok' => false, 'message' => 'Gagal menghapus pengaduan.'];
    }

    public function statistik() {
        $result = [
            'total' => 0,
            'menunggu' => 0,
            'diproses' => 0,
            'selesai' => 0
        ];

        $q = mysqli_query($this->conn,
            "SELECT status, COUNT(*) total FROM pengaduan GROUP BY status");
        while ($r = mysqli_fetch_assoc($q)) {
            $result['total'] += (int)$r['total'];
            if ($r['status'] === 'Menunggu') $result['menunggu'] = (int)$r['total'];
            if ($r['status'] === 'Diproses') $result['diproses'] = (int)$r['total'];
            if ($r['status'] === 'Selesai') $result['selesai'] = (int)$r['total'];
        }
        return $result;
    }
}
?>
