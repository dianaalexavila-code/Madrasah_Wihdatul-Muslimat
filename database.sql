CREATE DATABASE IF NOT EXISTS db_madrasah_wm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_madrasah_wm;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','operator') NOT NULL DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS teachers (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(160) NOT NULL,
  nik CHAR(16) NULL, phone VARCHAR(25) NULL, address TEXT NULL,
  education VARCHAR(120) NULL, position VARCHAR(120) NULL,
  homeroom_class TINYINT UNSIGNED NULL, subjects VARCHAR(255) NULL,
  photo VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_teacher_nik (nik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS students (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nomor_induk VARCHAR(40) NOT NULL UNIQUE,
  nisn VARCHAR(20) NULL, nama_lengkap VARCHAR(160) NOT NULL, nik CHAR(16) NULL,
  tempat_lahir VARCHAR(100) NULL, tanggal_lahir DATE NULL,
  jenis_kelamin ENUM('Laki-laki','Perempuan') NOT NULL, alamat TEXT NULL,
  rt_rw VARCHAR(30) NULL, kelurahan VARCHAR(100) NULL, kecamatan VARCHAR(100) NULL,
  kota VARCHAR(100) NULL, kelas TINYINT UNSIGNED NOT NULL, tahun_masuk YEAR NULL,
  nama_ayah VARCHAR(160) NULL, nik_ayah CHAR(16) NULL, pekerjaan_ayah VARCHAR(120) NULL, telepon_ayah VARCHAR(25) NULL,
  nama_ibu VARCHAR(160) NULL, nik_ibu CHAR(16) NULL, pekerjaan_ibu VARCHAR(120) NULL, telepon_ibu VARCHAR(25) NULL,
  nama_wali VARCHAR(160) NULL, hubungan_wali VARCHAR(80) NULL, telepon_wali VARCHAR(25) NULL, alamat_wali TEXT NULL,
  foto VARCHAR(255) NULL,
  status ENUM('Aktif','Lulus','Pindah','Tidak Aktif') NOT NULL DEFAULT 'Aktif',
  catatan TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_students_name (nama_lengkap), INDEX idx_students_class_status (kelas,status), INDEX idx_students_nisn (nisn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS classes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, class_number TINYINT UNSIGNED NOT NULL,
  academic_year VARCHAR(20) NOT NULL, room VARCHAR(80) NULL, teacher_id BIGINT UNSIGNED NULL,
  schedule TEXT NULL, UNIQUE KEY uq_class_year (class_number,academic_year),
  CONSTRAINT fk_class_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS attendance (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL,
  attendance_date DATE NOT NULL, status ENUM('Hadir','Izin','Sakit','Alpa') NOT NULL DEFAULT 'Hadir',
  note TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_attendance_day_student (student_id,attendance_date),
  CONSTRAINT fk_attendance_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS grades (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL,
  subject VARCHAR(120) NOT NULL, term VARCHAR(60) NOT NULL, score DECIMAL(5,2) NULL,
  development_note TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_grades_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS promotions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, student_id BIGINT UNSIGNED NOT NULL,
  from_class TINYINT UNSIGNED NOT NULL, to_class TINYINT UNSIGNED NOT NULL,
  academic_year VARCHAR(20) NOT NULL, promotion_date DATE NULL, note TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_promotions_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL,
  category VARCHAR(100) NULL, event_date DATE NULL, location VARCHAR(160) NULL,
  description TEXT NULL, photo VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS calendar_events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180) NOT NULL,
  academic_year VARCHAR(20) NOT NULL, event_date DATE NOT NULL, end_date DATE NULL,
  source VARCHAR(255) NULL, note TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_calendar_date (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS facilities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL,
  category VARCHAR(100) NULL, quantity INT UNSIGNED NULL,
  `condition` ENUM('Baik','Perlu Perbaikan','Rusak') NULL,
  location VARCHAR(160) NULL, note TEXT NULL, photo VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS organization (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, role VARCHAR(120) NOT NULL,
  person_name VARCHAR(160) NOT NULL, teacher_id BIGINT UNSIGNED NULL,
  sort_order INT NOT NULL DEFAULT 0, note TEXT NULL,
  CONSTRAINT fk_org_teacher FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
