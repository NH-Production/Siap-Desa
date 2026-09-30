-- SIAP-DESA MySQL foundation schema
-- MySQL 8.x / MariaDB 10.6+
-- Canonical schema used by Laravel models.

CREATE TABLE IF NOT EXISTS villages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  code VARCHAR(30) NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  district VARCHAR(150) NULL,
  regency VARCHAR(150) NULL,
  province VARCHAR(150) NULL,
  postal_code VARCHAR(10) NULL,
  address TEXT NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  website VARCHAR(255) NULL,
  head_name VARCHAR(190) NULL,
  secretary_name VARCHAR(190) NULL,
  letter_number_format VARCHAR(255) NULL,
  settings JSON NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  deleted_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL UNIQUE,
  display_name VARCHAR(150) NOT NULL,
  description VARCHAR(255) NULL,
  is_system TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL UNIQUE,
  display_name VARCHAR(150) NOT NULL,
  module VARCHAR(80) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NULL,
  name VARCHAR(150) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(190) NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
  last_login_at DATETIME NULL,
  remember_token VARCHAR(100) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  deleted_at DATETIME NULL,
  KEY idx_users_village (village_uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
  user_id BIGINT UNSIGNED NOT NULL,
  role_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (user_id, role_id),
  CONSTRAINT fk_ur_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS devices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NOT NULL,
  device_code VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  device_token_hash VARCHAR(255) NULL,
  app_version VARCHAR(30) NULL,
  schema_version VARCHAR(30) NULL,
  sync_protocol_version VARCHAR(30) NULL,
  last_seen_at DATETIME NULL,
  last_sync_at DATETIME NULL,
  status ENUM('active','blocked','retired') NOT NULL DEFAULT 'active',
  registered_at DATETIME NULL,
  revoked_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  KEY idx_devices_village_status (village_uuid, status),
  CONSTRAINT fk_device_village FOREIGN KEY (village_uuid) REFERENCES villages(uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NULL,
  device_uuid CHAR(36) NULL,
  user_uuid CHAR(36) NULL,
  module VARCHAR(80) NOT NULL,
  action VARCHAR(80) NOT NULL,
  table_name VARCHAR(120) NULL,
  record_uuid CHAR(36) NULL,
  old_data JSON NULL,
  new_data JSON NULL,
  ip_address VARCHAR(45) NULL,
  user_agent TEXT NULL,
  created_at DATETIME NOT NULL,
  KEY idx_audit_village_date (village_uuid, created_at),
  KEY idx_audit_record (table_name, record_uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sync_queue (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sync_event_uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NOT NULL,
  device_uuid CHAR(36) NOT NULL,
  table_name VARCHAR(120) NOT NULL,
  record_uuid CHAR(36) NOT NULL,
  operation ENUM('INSERT','UPDATE','DELETE') NOT NULL,
  record_version BIGINT UNSIGNED NOT NULL DEFAULT 1,
  payload JSON NULL,
  created_at DATETIME NOT NULL,
  synced_at DATETIME NULL,
  attempts INT UNSIGNED NOT NULL DEFAULT 0,
  last_attempt_at DATETIME NULL,
  last_error TEXT NULL,
  status ENUM('PENDING','PROCESSING','SYNCED','FAILED','CONFLICT') NOT NULL DEFAULT 'PENDING',
  KEY idx_sync_pending (village_uuid, status, id),
  KEY idx_sync_record (table_name, record_uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sync_checkpoints (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  village_uuid CHAR(36) NOT NULL,
  device_uuid CHAR(36) NOT NULL,
  remote_cursor VARCHAR(190) NULL,
  last_push_at DATETIME NULL,
  last_pull_at DATETIME NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uq_checkpoint (village_uuid, device_uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sync_conflicts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NOT NULL,
  device_uuid CHAR(36) NOT NULL,
  table_name VARCHAR(120) NOT NULL,
  record_uuid CHAR(36) NOT NULL,
  local_version BIGINT UNSIGNED NULL,
  remote_version BIGINT UNSIGNED NULL,
  local_payload JSON NULL,
  remote_payload JSON NULL,
  resolution ENUM('PENDING','LOCAL','REMOTE','MERGED') NOT NULL DEFAULT 'PENDING',
  resolved_by CHAR(36) NULL,
  resolved_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  KEY idx_conflict_status (village_uuid, resolution, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS system_settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  is_secret TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
