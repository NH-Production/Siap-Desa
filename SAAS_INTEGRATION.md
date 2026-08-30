# SIAP DESA - SAAS & GITHUB INTEGRATION ARCHITECTURE

Sistem Informasi Administrasi Pemerintahan Desa (SIAP Desa) dirancang dengan arsitektur **Hybrid Local-First + Central Multi-Tenant SAAS**.

## 1. Topologi Arsitektur SAAS

```
+-----------------------------------------------------------------------------------+
|                            GITHUB CENTRAL REPOSITORY                              |
|           (Source Code, GitHub Actions CI/CD, Automated OTA Releases)             |
+----------------------------------------+------------------------------------------+
                                         |
                                         v
+-----------------------------------------------------------------------------------+
|                        CENTRAL CLOUD SAAS BACKEND                                 |
|            (Supabase / Central PostgreSQL, License Verification, Sync API)        |
+-------------------+-----------------------------+---------------------------------+
                    |                             |
     Delta Sync API |              Delta Sync API |              Delta Sync API
                    v                             v                             v
           +------------------+          +------------------+          +------------------+
           | DESA SINDANGRESMI|          |   DESA SUKAMAJU  |          |   DESA MEKARSARI |
           | (Local SQLite DB)|          | (Local SQLite DB)|          | (Local SQLite DB)|
           +------------------+          +------------------+          +------------------+
```

## 2. Fitur Integrasi SAAS
1. **Multi-Tenant Data Isolation**: Setiap transaksi dan entitas data memiliki `village_id` unik.
2. **License Activation Engine**: Validasi lisensi desa otomatis via token `SIAP-SAAS-XXXX-XXXX`.
3. **Automated Over-The-Air (OTA) Updates**: Aplikasi desktop memeriksa rilis patch terbaru dari GitHub Releases.
4. **Offline Resiliency**: Jika koneksi terputus, aplikasi tetap berjalan 100% menggunakan database lokal di komputer desa.
