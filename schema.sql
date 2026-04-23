-- 1. Tabel User & Autentikasi
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'mahasiswa', 'dosen_pembimbing', 'dosen_prodi', 'supervisor_industri') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Profil (Entitas Utama)
CREATE TABLE mahasiswa (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    nim VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    prodi VARCHAR(50),
    cv_file_path VARCHAR(255), -- File CV awal mahasiswa
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE industri (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    nama_perusahaan VARCHAR(100) NOT NULL,
    alamat TEXT,
    kontak_person VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE dosen (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    nip VARCHAR(20) UNIQUE NOT NULL,
    nama_dosen VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. Tabel Seleksi (Pendaftaran & Keputusan Industri)
CREATE TABLE pendaftaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mahasiswa_id INT NOT NULL,
    industri_id INT NOT NULL,
    tanggal_daftar TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_seleksi ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    keterangan_industri TEXT, -- Alasan jika ditolak (Jalur Merah di Flowchart)
    FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id),
    FOREIGN KEY (industri_id) REFERENCES industri(id)
);

-- 4. Tabel Magang Aktif (Setelah diterima & Persiapan)
CREATE TABLE magang_aktif (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pendaftaran_id INT UNIQUE NOT NULL,
    -- Bagian Persiapan & Perizinan
    file_agreement_industri VARCHAR(255), -- Upload by Industri
    file_agreement_mahasiswa VARCHAR(255), -- Signed by Mahasiswa
    sk_pembimbing_path VARCHAR(255), -- Upload by Admin
    
    -- Penempatan Supervisor
    supervisor_kampus_id INT, -- Assigned by Admin
    supervisor_industri_id INT, -- Ditentukan industri
    
    -- Progress & Periode
    status_tahapan ENUM('persiapan', 'pelaksanaan', 'penutupan', 'lulus') DEFAULT 'persiapan',
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    
    FOREIGN KEY (pendaftaran_id) REFERENCES pendaftaran(id),
    FOREIGN KEY (supervisor_kampus_id) REFERENCES dosen_pembimbing(id),
    FOREIGN KEY (supervisor_industri_id) REFERENCES users(id) -- Supervisor industri seringkali user industri itu sendiri atau stafnya
);

-- 5. Tabel Pelaksanaan (Harian)
CREATE TABLE logbook (
    id INT PRIMARY KEY AUTO_INCREMENT,
    magang_id INT NOT NULL,
    tanggal DATE NOT NULL,
    kegiatan TEXT NOT NULL,
    status_presensi ENUM('hadir', 'izin', 'sakit') DEFAULT 'hadir',
    is_approved_industri BOOLEAN DEFAULT FALSE,
    komentar_industri TEXT,
    is_checked_kampus BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (magang_id) REFERENCES magang_aktif(id)
);

-- 6. Tabel Penutupan (Laporan & Nilai)
CREATE TABLE laporan_akhir (
    id INT PRIMARY KEY AUTO_INCREMENT,
    magang_id INT UNIQUE NOT NULL,
    file_laporan VARCHAR(255) NOT NULL,
    tanggal_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status_approval_kampus ENUM('pending', 'revisi', 'disetujui') DEFAULT 'pending',
    catatan_revisi TEXT,
    FOREIGN KEY (magang_id) REFERENCES magang_aktif(id)
);

CREATE TABLE penilaian (
    id INT PRIMARY KEY AUTO_INCREMENT,
    magang_id INT UNIQUE NOT NULL,
    nilai_industri DECIMAL(5,2),
    nilai_kampus DECIMAL(5,2),
    nilai_akhir DECIMAL(5,2) GENERATED ALWAYS AS ((nilai_industri + nilai_kampus) / 2) STORED,
    status_verifikasi_admin BOOLEAN DEFAULT FALSE, -- Verifikasi Kelengkapan Data & Nilai
    FOREIGN KEY (magang_id) REFERENCES magang_aktif(id)
);

-- 7. Tabel Output (Sertifikat)
CREATE TABLE sertifikat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    magang_id INT UNIQUE NOT NULL,
    nomor_sertifikat VARCHAR(100) UNIQUE,
    file_sertifikat_path VARCHAR(255),
    tanggal_terbit DATE,
    FOREIGN KEY (magang_id) REFERENCES magang_aktif(id)
);