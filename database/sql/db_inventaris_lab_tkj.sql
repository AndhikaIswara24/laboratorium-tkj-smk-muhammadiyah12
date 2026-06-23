-- =============================================
-- DATABASE: db_inventaris_lab_tkj
-- =============================================
CREATE DATABASE IF NOT EXISTS db_inventaris_lab_tkj 
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_inventaris_lab_tkj;

-- Tabel master aset
CREATE TABLE t_aset (
  id_aset       INT AUTO_INCREMENT PRIMARY KEY,
  kode_brg      VARCHAR(20) NOT NULL UNIQUE,
  nama_brg      VARCHAR(100) NOT NULL,
  merk_tipe     VARCHAR(80),
  spesifikasi   TEXT,
  lokasi        VARCHAR(60),
  thn_perolehan YEAR,
  harga_perolehan DECIMAL(15,2),
  asal_usul     ENUM('Pembelian','Hibah','Dropping Dinas','Dana BOS') DEFAULT 'Pembelian',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel 1: kondisi fisik & teknis
CREATE TABLE t_kondisi_fisik (
  id_kondisi    INT AUTO_INCREMENT PRIMARY KEY,
  id_aset       INT NOT NULL,
  tgl_observasi DATE NOT NULL,
  kondisi_brg   ENUM('B','RR','RB') NOT NULL,
  ket_teknis    VARCHAR(100),
  usia_pakai    INT,
  frq_kerusakan INT DEFAULT 0,
  kelas_label   ENUM('Layak','Perlu Servis','Tidak Layak') NOT NULL,
  FOREIGN KEY (id_aset) REFERENCES t_aset(id_aset)
);

-- Tabel 2: pemeliharaan
CREATE TABLE t_pemeliharaan (
  id_pm         INT AUTO_INCREMENT PRIMARY KEY,
  id_aset       INT NOT NULL,
  tgl_pm        DATE,
  jenis_pm      ENUM('Preventif','Korektif','Tidak Ada') DEFAULT 'Preventif',
  interval_bulan INT,
  pelaksana     VARCHAR(60),
  biaya_servis  DECIMAL(12,2) DEFAULT 0,
  kon_after     ENUM('B','RR','RB'),
  ket_pm        TEXT,
  FOREIGN KEY (id_aset) REFERENCES t_aset(id_aset)
);

-- Tabel 3: efisiensi output
CREATE TABLE t_efisiensi (
  id_efisiensi  INT AUTO_INCREMENT PRIMARY KEY,
  id_aset       INT NOT NULL,
  tgl_observasi DATE NOT NULL,
  jam_ops       FLOAT,
  penggunaan    ENUM('Tinggi','Sedang','Tidak Pakai'),
  jml_user      INT DEFAULT 0,
  downtime      FLOAT DEFAULT 0,
  perform       ENUM('Normal','Lambat','Mati'),
  umur_ekonomis INT,
  efi_out       ENUM('Tinggi','Sedang','Rendah'),
  FOREIGN KEY (id_aset) REFERENCES t_aset(id_aset)
);

-- Tabel 4: variabel eksternal
CREATE TABLE t_variabel_eksternal (
  id_eksternal  INT AUTO_INCREMENT PRIMARY KEY,
  id_aset       INT NOT NULL,
  tgl_observasi DATE NOT NULL,
  lingkungan    ENUM('Baik','Cukup','Buruk'),
  daya_listrik  ENUM('Stabil','Tidak Stabil','Sering Padam'),
  sparepart     ENUM('Tersedia','Terbatas','Tidak Ada'),
  anggaran      ENUM('Mendukung','Terbatas','Tidak Ada'),
  ext_effect    ENUM('Rendah','Sedang','Tinggi'),
  FOREIGN KEY (id_aset) REFERENCES t_aset(id_aset)
);

-- Tabel dataset Naive Bayes (hasil JOIN 4 tabel)
CREATE TABLE t_naive_bayes_dataset (
  id_dataset    INT AUTO_INCREMENT PRIMARY KEY,
  id_aset       INT NOT NULL,
  kondisi_brg   ENUM('B','RR','RB'),
  usia_pakai    INT,
  frq_kerusakan INT,
  jenis_pm      ENUM('Preventif','Korektif','Tidak Ada'),
  interval_pm   INT,
  efi_out       ENUM('Tinggi','Sedang','Rendah'),
  downtime      FLOAT,
  lingkungan    ENUM('Baik','Cukup','Buruk'),
  daya_listrik  ENUM('Stabil','Tidak Stabil','Sering Padam'),
  sparepart     ENUM('Tersedia','Terbatas','Tidak Ada'),
  kelas_label   ENUM('Layak','Perlu Servis','Tidak Layak'),
  tgl_input     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_aset) REFERENCES t_aset(id_aset)
);

-- Tabel hasil prediksi Naive Bayes
CREATE TABLE t_hasil_prediksi (
  id_prediksi     INT AUTO_INCREMENT PRIMARY KEY,
  id_dataset      INT NOT NULL,
  id_aset         INT NOT NULL,
  tgl_prediksi    DATETIME DEFAULT CURRENT_TIMESTAMP,
  hasil_prediksi  ENUM('Layak','Perlu Servis','Tidak Layak'),
  prob_layak      FLOAT,
  prob_servis     FLOAT,
  prob_tidak_layak FLOAT,
  FOREIGN KEY (id_dataset) REFERENCES t_naive_bayes_dataset(id_dataset),
  FOREIGN KEY (id_aset) REFERENCES t_aset(id_aset)
);
