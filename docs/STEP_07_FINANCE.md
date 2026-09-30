# STEP 07 — APBDes & Keuangan Desa

## Komponen

- Chart of Accounts / Kode Rekening.
- Pagu anggaran per tahun.
- Buku transaksi penerimaan, pengeluaran, dan mutasi.
- Saldo kas berdasarkan transaksi POSTED.
- Nomor transaksi otomatis.
- Bukti transaksi dengan SHA-256.
- SPJ per transaksi.
- Workflow SPJ DRAFT → SUBMITTED → VERIFIED/REJECTED.
- Audit log dan Sync Queue.

## Prinsip data

Transaksi keuangan menggunakan UUID dan version sehingga dapat direplikasi sebagai delta. Status POSTED dicatat bersama pengguna dan waktu posting. Data VOID tidak dihapus dari database sehingga histori tetap tersedia.

## Saldo

Saldo operasional dihitung dari:

PENERIMAAN POSTED - PENGELUARAN POSTED

MUTASI tidak dianggap sebagai penerimaan/pengeluaran baru tanpa aturan rekening lawan yang lengkap.

## SPJ

Satu transaksi dapat memiliki maksimal satu SPJ melalui constraint UNIQUE(transaction_id). Nomor SPJ dibuat lokal dan ikut disinkronkan.

## Bukti

File bukti disimpan pada storage lokal Windows. Metadata dan checksum SHA-256 dicatat di database. Sinkronisasi file nantinya dilakukan melalui object/file transfer yang terpisah dari event database.

## Offline

Semua pencatatan APBDes dan SPJ dilakukan pada MySQL lokal. Tidak ada ketergantungan terhadap internet untuk input transaksi.
