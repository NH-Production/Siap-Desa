-- SIAP-DESA MySQL foundation schema
-- Target: MySQL 8.x / MariaDB 10.6+

CREATE TABLE IF NOT EXISTS villages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  code VARCHAR(30) NULL,
  name VARCHAR(150) NOT NULL,
  district VARCHAR(150) NULL,
  regency VARCHAR(150) NULL,
  province VARCHAR(150) NULL,
  postal_code VARCHAR(10) NULL,
  address TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  deleted_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS devices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NOT NULL,
  device_name VARCHAR(150) NOT NULL,
  device_token_hash VARCHAR(255) NULL,
  app_version VARCHAR(30) NULL,
  schema_version VARCHAR(30) NULL,
  last_seen_at DATETIME NULL,
  last_sync_at DATETIME NULL,
  status ENUM('active','blocked','retired') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uq_device_village (village_uuid, uuid)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  name VARCHAR(80) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  village_uuid CHAR(36) NULL,
  role_id BIGINT UNSIGNED NULL,
  name VARCHAR(150) NOT NULL,
  username VARCHAR(80) NOT NULL,
  email VARCHAR(190) NULL,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  deleted_at DATETIME NULL,
  UNIQUE KEY uq_user_username (username),
  UNIQUE KEY uq_user_email (email),
  CONSTRAINT fk_user_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS permissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  uuid CHAR(36) NOT NULL UNIQUE,
  code VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS role_permissions (
  role_id BIGINT UNSIGNED NOT NULL,
  permission_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
  CONSTRAINT fk_rp_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

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
  last_error TEXT NULL,
  status ENUM('PENDING','PROCESSING','SYNCED','FAILED','CONFLICT') NOT NULL DEFAULT 'PENDING',
  KEY idx_sync_pending (village_uuid, status, id),
  KEY idx_sync_record (table_name, record_uuid)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sync_checkpoints (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  village_uuid CHAR(36) NOT NULL,
  device_uuid CHAR(36) NOT NULL,
  remote_cursor VARCHAR(190) NULL,
  last_push_at DATETIME NULL,
  last_pull_at DATETIME NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uq_checkpoint (village_uuid, device_uuid)
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS system_settings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  is_secret TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB;
