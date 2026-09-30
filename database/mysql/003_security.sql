ALTER TABLE users ADD COLUMN IF NOT EXISTS village_uuid CHAR(36) NULL AFTER uuid;
ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active';
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at DATETIME NULL;
ALTER TABLE devices ADD COLUMN IF NOT EXISTS device_token_hash VARCHAR(255) NULL;
ALTER TABLE audit_logs ADD INDEX idx_audit_user_date (user_uuid, created_at);
ALTER TABLE audit_logs ADD INDEX idx_audit_device_date (device_uuid, created_at);
