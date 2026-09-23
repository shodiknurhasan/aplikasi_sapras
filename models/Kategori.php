<?php
class Kategori {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function tampil() {
        return mysqli_query($this->conn, "SELECT * FROM kategori ORDER BY id_kategori ASC");
    }

    public function getById($id) {
        $id = (int)$id;
        $q = mysqli_query($this->conn, "SELECT * FROM kategori WHERE id_kategori=$id");
        return mysqli_fetch_assoc($q);
    }

    public function tambah($nama, $deskripsi) {
        $nama = mysqli_real_escape_string($this->conn, trim($nama));
        $deskripsi = mysqli_real_escape_string($this->conn, trim($deskripsi));
        return mysqli_query($this->conn,
            "INSERT INTO kategori (nama_kategori,deskripsi,aktif)
             VALUES ('$nama','$deskripsi',1)");
    }

    public function ubah($id, $nama, $deskripsi) {
        $id = (int)$id;
        $nama = mysqli_real_escape_string($this->conn, trim($nama));
        $deskripsi = mysqli_real_escape_string($this->conn, trim($deskripsi));
        return mysqli_query($this->conn,
            "UPDATE kategori SET nama_kategori='$nama', deskripsi='$deskripsi'
             WHERE id_kategori=$id");
    }

    public function hapus($id) {
        $id = (int)$id;
        $cek = mysqli_query($this->conn,
            "SELECT COUNT(*) AS total FROM pengaduan WHERE id_kategori=$id");
        $row = mysqli_fetch_assoc($cek);
        if ((int)$row['total'] > 0) return false;

        return mysqli_query($this->conn, "DELETE FROM kategori WHERE id_kategori=$id");
    }

    public function ubahAktif($id) {
        $id = (int)$id;
        return mysqli_query($this->conn,
            "UPDATE kategori SET aktif = IF(aktif=1,0,1) WHERE id_kategori=$id");
    }
}
?>
