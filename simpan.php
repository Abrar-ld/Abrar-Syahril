<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "klinik_db"; 

$conn = mysqli_connect($host, $user, $pass, $db);


if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$pesan_sukses = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $nama_pasien    = mysqli_real_escape_string($conn, $_POST['nama_pasien']);
    $tanggal_lahir  = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $no_rekam_medis = mysqli_real_escape_string($conn, $_POST['no_rekam_medis']);
    $domisili       = mysqli_real_escape_string($conn, $_POST['domisili']);
    $kontak_darurat = mysqli_real_escape_string($conn, $_POST['kontak_darurat']);

    $query = "INSERT INTO pasien (nama_pasien, tanggal_lahir, no_rekam_medis, domisili, kontak_darurat) 
              VALUES ('$nama_pasien', '$tanggal_lahir', '$no_rekam_medis', '$domisili', '$kontak_darurat')";

    if (mysqli_query($conn, $query)) {
        $pesan_sukses = "Data pasien berhasil disimpan!";
    }
}


if (isset($_GET['hapus'])) {
    $id_pasien = intval($_GET['hapus']);
    $query_hapus = "DELETE FROM pasien WHERE id = $id_pasien";
    
    if (mysqli_query($conn, $query_hapus)) {
        
        header("Location: simpan.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Data Pasien - Klinik Rawat Jalan</title>
  <link rel="stylesheet" href="mnjmpasien.css">
  <style>
    .alert-success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
    .data-section { margin-top: 40px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    table.data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    table.data-table th, table.data-table td { border: 1px solid #ddd; padding: 10px; text-align: left; font-size: 14px; }
    table.data-table th { background-color: #f4f6f8; }
    .btn-danger { background-color: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 13px; }
    .btn-danger:hover { background-color: #c82333; }
  </style>
</head>
<body>

  <header class="topbar">
    <span class="brand">Klinik Rawat Jalan</span>
    <nav>
      <a href="#" class="active">Pasien</a>
    </nav>
  </header>

  <main class="container">
    <div class="page-head">
      <h1>Tambah data pasien</h1>
      <p>Isi profil dasar pasien. Kolom bertanda <span class="req">*</span> wajib diisi.</p>
    </div>

    
    <?php if (!empty($pesan_sukses)) : ?>
      <div class="alert-success"><?php echo $pesan_sukses; ?></div>
    <?php endif; ?>

    
    <form class="card" action="" method="POST">

      <fieldset>
        <legend>Identitas pasien</legend>

        <div class="field">
          <label for="nama_pasien">Nama pasien <span class="req">*</span></label>
          <input type="text" id="nama_pasien" name="nama_pasien"
                 placeholder="Contoh: Siti Rahmawati" 
                 value="<?php echo isset($_POST['nama_pasien']) ? htmlspecialchars($_POST['nama_pasien']) : ''; ?>" required>
        </div>

        <div class="row">
          <div class="field">
            <label for="tanggal_lahir">Tanggal lahir <span class="req">*</span></label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" 
                   value="<?php echo isset($_POST['tanggal_lahir']) ? htmlspecialchars($_POST['tanggal_lahir']) : ''; ?>" required>
          </div>

          <div class="field">
            <label for="no_rekam_medis">No. rekam medis <span class="req">*</span></label>
            <input type="text" id="no_rekam_medis" name="no_rekam_medis"
                   placeholder="Contoh: RM-000123" 
                   value="<?php echo isset($_POST['no_rekam_medis']) ? htmlspecialchars($_POST['no_rekam_medis']) : ''; ?>" required>
            <small>Nomor unik untuk setiap pasien.</small>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Domisili &amp; kontak</legend>

        <div class="field">
          <label for="domisili">Domisili <span class="req">*</span></label>
          <textarea id="domisili" name="domisili" rows="3"
                    placeholder="Alamat tempat tinggal saat ini" required><?php echo isset($_POST['domisili']) ? htmlspecialchars($_POST['domisili']) : ''; ?></textarea>
        </div>

        <div class="field">
          <label for="kontak_darurat">Kontak darurat <span class="req">*</span></label>
          <input type="tel" id="kontak_darurat" name="kontak_darurat"
                 placeholder="Contoh: 081234567890" pattern="[0-9+\- ]{8,15}" 
                 value="<?php echo isset($_POST['kontak_darurat']) ? htmlspecialchars($_POST['kontak_darurat']) : ''; ?>" required>
          <small>Nomor keluarga atau orang terdekat yang bisa dihubungi.</small>
        </div>
      </fieldset>

      <div class="actions">
        
        <button type="reset" class="btn btn-ghost">Reset</button>
        <button type="submit" name="simpan" class="btn btn-primary">Simpan pasien</button>
      </div>

    </form>

    
    <div class="data-section">
      <h2>Daftar Pasien Terdaftar</h2>
      <table class="data-table">
        <thead>
          <tr>
            <th>No</th>
            <th>No. Rekam Medis</th>
            <th>Nama Pasien</th>
            <th>Tanggal Lahir</th>
            <th>Domisili</th>
            <th>Kontak Darurat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = mysqli_query($conn, "SELECT * FROM pasien ORDER BY id DESC");
          $no = 1;
          
          if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $no++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['no_rekam_medis']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['nama_pasien']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['tanggal_lahir']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['domisili']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['kontak_darurat']) . "</td>";
                  echo "<td><a href='simpan.php?hapus=" . $row['id'] . "' class='btn-danger' onclick=\"return confirm('Yakin ingin menghapus data pasien ini?');\">Hapus</a></td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data pasien.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

  </main>

</body>
</html>