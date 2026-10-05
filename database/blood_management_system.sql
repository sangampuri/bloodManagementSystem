-- Blood Management System : full schema + demo data
-- Import via phpMyAdmin -> Import tab (server level, not inside a database)

CREATE DATABASE IF NOT EXISTS blood_management_system
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blood_management_system;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS user_notifications, blood_requests, blood_inventories, donors, users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------
-- users
-- ---------------------------------------------------------
CREATE TABLE users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  email_verified_at TIMESTAMP NULL DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  remember_token VARCHAR(100) DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email),
  KEY users_role_index (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- donors (one profile per user)
-- ---------------------------------------------------------
CREATE TABLE donors (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  gender ENUM('male','female','other') NOT NULL,
  age TINYINT UNSIGNED NOT NULL,
  phone VARCHAR(20) NOT NULL,
  location VARCHAR(100) NOT NULL,
  last_donation_date DATE DEFAULT NULL,
  is_available TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY donors_user_id_unique (user_id),
  KEY donors_search_index (blood_group, location),
  CONSTRAINT donors_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT donors_age_check CHECK (age BETWEEN 18 AND 65)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- blood_inventories (one row = one batch of units)
-- ---------------------------------------------------------
CREATE TABLE blood_inventories (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  batch_code VARCHAR(30) NOT NULL,
  units SMALLINT UNSIGNED NOT NULL,
  collected_on DATE NOT NULL,
  expiry_date DATE NOT NULL,
  added_by BIGINT UNSIGNED DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY blood_inventories_batch_code_unique (batch_code),
  KEY blood_inventories_group_expiry_index (blood_group, expiry_date),
  CONSTRAINT blood_inventories_added_by_foreign FOREIGN KEY (added_by)
    REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT blood_inventories_units_check CHECK (units > 0),
  CONSTRAINT blood_inventories_dates_check CHECK (expiry_date >= collected_on)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- blood_requests
-- ---------------------------------------------------------
CREATE TABLE blood_requests (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  donor_id BIGINT UNSIGNED DEFAULT NULL,
  blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  quantity TINYINT UNSIGNED NOT NULL,
  hospital_name VARCHAR(150) NOT NULL,
  hospital_address VARCHAR(255) NOT NULL,
  contact_number VARCHAR(20) NOT NULL,
  urgency ENUM('normal','urgent','critical') NOT NULL DEFAULT 'normal',
  reason VARCHAR(255) NOT NULL,
  message TEXT DEFAULT NULL,
  status ENUM('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  admin_note VARCHAR(255) DEFAULT NULL,
  reviewed_by BIGINT UNSIGNED DEFAULT NULL,
  reviewed_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY blood_requests_status_index (status),
  KEY blood_requests_user_status_index (user_id, status),
  KEY blood_requests_donor_id_index (donor_id),
  CONSTRAINT blood_requests_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT blood_requests_donor_id_foreign FOREIGN KEY (donor_id)
    REFERENCES donors (id) ON DELETE SET NULL,
  CONSTRAINT blood_requests_reviewed_by_foreign FOREIGN KEY (reviewed_by)
    REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT blood_requests_quantity_check CHECK (quantity > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- user_notifications
-- ---------------------------------------------------------
CREATE TABLE user_notifications (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  message VARCHAR(255) NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY user_notifications_user_read_index (user_id, is_read),
  CONSTRAINT user_notifications_user_id_foreign FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- DEMO DATA (all fictional)
-- Admin password : Admin@12345
-- User password  : User@12345  (same for every demo user)
-- =========================================================

INSERT INTO users (id, name, email, password, role, created_at, updated_at) VALUES
(1, 'Blood Bank Admin', 'admin@example.com',  '$2y$10$.uP28wxqI.tXBIAmFEGLve82pZOczjjfcHps0WQiUhJZHdI0ga6zy', 'admin', '2026-08-01 09:00:00', '2026-08-01 09:00:00'),
(2, 'Aarav Sharma',     'aarav@example.com',  '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-03 10:00:00', '2026-08-03 10:00:00'),
(3, 'Sita Rai',         'sita@example.com',   '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-04 10:00:00', '2026-08-04 10:00:00'),
(4, 'Bikash Limbu',     'bikash@example.com', '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-05 10:00:00', '2026-08-05 10:00:00'),
(5, 'Anita Karki',      'anita@example.com',  '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-06 10:00:00', '2026-08-06 10:00:00'),
(6, 'Rohan Thapa',      'rohan@example.com',  '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-07 10:00:00', '2026-08-07 10:00:00'),
(7, 'Maya Gurung',      'maya@example.com',   '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-08 10:00:00', '2026-08-08 10:00:00'),
(8, 'Dipesh Shrestha',  'dipesh@example.com', '$2y$10$BJX3ww/sijx1RC3aP6oBQejJ/0f2ie5pTmUTbMpuep4fSk3ycVcuC', 'user',  '2026-08-09 10:00:00', '2026-08-09 10:00:00');

-- Users 2-7 are donors. User 8 is requester only.
-- Sita (id 3) donated 2026-08-30, so marked unavailable.
INSERT INTO donors (id, user_id, blood_group, gender, age, phone, location, last_donation_date, is_available, created_at, updated_at) VALUES
(1, 2, 'A+',  'male',   24, '9800000102', 'Itahari',     '2026-05-14', 1, '2026-08-03 11:00:00', '2026-08-03 11:00:00'),
(2, 3, 'O-',  'female', 29, '9800000103', 'Dharan',      '2026-08-30', 0, '2026-08-04 11:00:00', '2026-09-01 08:00:00'),
(3, 4, 'B+',  'male',   31, '9800000104', 'Biratnagar',  NULL,         1, '2026-08-05 11:00:00', '2026-08-05 11:00:00'),
(4, 5, 'O+',  'female', 22, '9800000105', 'Kathmandu',   '2026-03-02', 1, '2026-08-06 11:00:00', '2026-08-06 11:00:00'),
(5, 6, 'AB+', 'male',   35, '9800000106', 'Itahari',     '2026-06-20', 1, '2026-08-07 11:00:00', '2026-08-07 11:00:00'),
(6, 7, 'A-',  'female', 27, '9800000107', 'Pokhara',     NULL,         1, '2026-08-08 11:00:00', '2026-08-08 11:00:00');

-- Batch 10 is expired, batch 2 expires soon. Good for expiry demo.
INSERT INTO blood_inventories (id, blood_group, batch_code, units, collected_on, expiry_date, added_by, created_at, updated_at) VALUES
(1,  'A+',  'BMS-2609-001', 12, '2026-09-10', '2026-10-22', 1, '2026-09-10 12:00:00', '2026-09-10 12:00:00'),
(2,  'A+',  'BMS-2608-014',  3, '2026-08-20', '2026-10-01', 1, '2026-08-20 12:00:00', '2026-08-20 12:00:00'),
(3,  'A-',  'BMS-2609-002',  5, '2026-09-12', '2026-10-24', 1, '2026-09-12 12:00:00', '2026-09-12 12:00:00'),
(4,  'B+',  'BMS-2609-003', 10, '2026-09-15', '2026-10-27', 1, '2026-09-15 12:00:00', '2026-09-15 12:00:00'),
(5,  'B-',  'BMS-2609-004',  3, '2026-09-05', '2026-10-17', 1, '2026-09-05 12:00:00', '2026-09-05 12:00:00'),
(6,  'AB+', 'BMS-2609-005',  8, '2026-09-18', '2026-10-30', 1, '2026-09-18 12:00:00', '2026-09-18 12:00:00'),
(7,  'AB-', 'BMS-2609-006',  2, '2026-09-20', '2026-11-01', 1, '2026-09-20 12:00:00', '2026-09-20 12:00:00'),
(8,  'O+',  'BMS-2609-007', 20, '2026-09-22', '2026-11-03', 1, '2026-09-22 12:00:00', '2026-09-22 12:00:00'),
(9,  'O-',  'BMS-2609-008',  4, '2026-09-22', '2026-11-03', 1, '2026-09-22 12:00:00', '2026-09-22 12:00:00'),
(10, 'O+',  'BMS-2608-003',  6, '2026-08-01', '2026-09-12', 1, '2026-08-01 12:00:00', '2026-08-01 12:00:00');

INSERT INTO blood_requests
(id, user_id, donor_id, blood_group, quantity, hospital_name, hospital_address, contact_number, urgency, reason, message, status, admin_note, reviewed_by, reviewed_at, created_at, updated_at) VALUES
(1, 8, 1,    'A+',  1, 'Riverside Medical Center',   'Ward 5, Biratnagar',  '9800000208', 'urgent',   'Scheduled surgery',      'Surgery is on Thursday morning. Please respond soon.', 'pending',   NULL, NULL, NULL, '2026-09-26 09:30:00', '2026-09-26 09:30:00'),
(2, 8, NULL, 'O+',  2, 'Valley General Hospital',    'Main Road, Dharan',   '9800000208', 'normal',   'Post-delivery recovery', NULL,                                                  'approved',  'Stock reserved. Collect at blood bank counter.', 1, '2026-09-11 14:00:00', '2026-09-10 16:20:00', '2026-09-11 14:00:00'),
(3, 4, NULL, 'AB-', 3, 'Sunrise Community Hospital', 'Station Road, Itahari','9800000204', 'critical', 'Accident case',          'Patient is in ICU.',                                   'rejected',  'Only 2 units in stock. Please request 2 or contact donors.', 1, '2026-09-18 08:15:00', '2026-09-18 07:50:00', '2026-09-18 08:15:00'),
(4, 5, 5,    'AB+', 1, 'Sunrise Community Hospital', 'Station Road, Itahari','9800000205', 'critical', 'Emergency transfusion',  NULL,                                                  'completed', 'Donation done.', 1, '2026-08-25 18:00:00', '2026-08-25 15:10:00', '2026-08-27 10:00:00'),
(5, 3, NULL, 'B-',  1, 'Valley General Hospital',    'Main Road, Dharan',   '9800000203', 'normal',   'Planned procedure',      NULL,                                                  'cancelled', NULL, NULL, NULL, '2026-09-02 11:00:00', '2026-09-03 09:00:00');

INSERT INTO user_notifications (id, user_id, message, is_read, created_at, updated_at) VALUES
(1, 8, 'Your blood request has been submitted successfully.',           1, '2026-09-10 16:20:00', '2026-09-10 16:20:00'),
(2, 8, 'Your request has been approved.',                               0, '2026-09-11 14:00:00', '2026-09-11 14:00:00'),
(3, 4, 'Your request has been rejected. Only 2 units in stock.',        1, '2026-09-18 08:15:00', '2026-09-18 08:15:00'),
(4, 2, 'Your donor registration has been completed.',                   1, '2026-08-03 11:00:00', '2026-08-03 11:00:00'),
(5, 2, 'Someone sent you a blood request. Check your requests page.',   0, '2026-09-26 09:30:00', '2026-09-26 09:30:00');