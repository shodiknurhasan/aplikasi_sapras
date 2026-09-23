<?php
class Siswa {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function tampil() {
        return mysqli_query($this->conn, "SELECT * FROM siswa ORDER BY nis ASC");
    }

    public function getByNis($nis) {
        $nis = (int)$nis;
        $q = mysqli_query($this->conn, "SELECT * FROM siswa WHERE nis=$nis");
        return mysqli_fetch_assoc($q);
    }

    public function tambah($nis, $nama, $kelas, $username, $password) {
        $nis = (int)$nis;
        $nama = mysqli_real_escape_string($this->conn, trim($nama));
        $kelas = mysqli_real_escape_string($this->conn, trim($kelas));
        $username = mysqli_real_escape_string($this->conn, trim($username));
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO siswa (nis,nama,kelas,username,password,aktif)
                VALUES ($nis,'$nama','$kelas','$username','$hash',1)";
        return mysqli_query($this->conn, $sql);
    }

  public function ubah($nis_lama, $nis_baru, $nama, $kelas, $username, $password = '') {
    $nis_lama = (int)$nis_lama;
    $nis_baru = (int)$nis_baru;

    $nama = mysqli_real_escape_string($this->conn, trim($nama));
    $kelas = mysqli_real_escape_string($this->conn, trim($kelas));
    $username = mysqli_real_escape_string($this->conn, trim($username));

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE siswa 
                SET nis=$nis_baru,
                    nama='$nama',
                    kelas='$kelas',
                    username='$username',
                    password='$hash'
                WHERE nis=$nis_lama";
    } else {
        $sql = "UPDATE siswa 
                SET nis=$nis_baru,
                    nama='$nama',
                    kelas='$kelas',
                    username='$username'
                WHERE nis=$nis_lama";
    }

   $result = mysqli_query($this->conn, $sql);

if (!$result) {
    die("Error SQL: " . mysqli_error($this->conn));
}

return $result;

}


    public function hapus($nis) {
        $nis = (int)$nis;
        // Pengaduan yang sudah dimiliki siswa tidak boleh membuat FK rusak.
        $cek = mysqli_query($this->conn, "SELECT COUNT(*) AS total FROM pengaduan WHERE nis=$nis");
        $row = mysqli_fetch_assoc($cek);
        if ((int)$row['total'] > 0) return false;

        return mysqli_query($this->conn, "DELETE FROM siswa WHERE nis=$nis");
    }

    public function ubahAktif($nis) {
        $nis = (int)$nis;
        return mysqli_query($this->conn,
            "UPDATE siswa SET aktif = IF(aktif=1,0,1) WHERE nis=$nis");
    }
}
?>
