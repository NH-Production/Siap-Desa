# STEP 08 — Aset Desa, Inventaris & Bansos

## Aset

- Kategori aset.
- Kode aset unik.
- Nilai perolehan.
- Sumber perolehan.
- Kondisi.
- Lokasi.
- Penanggung jawab.
- Status aktif/dihapuskan/dipindahkan.
- Riwayat mutasi.

Kode aset menggunakan pola AST-{TAHUN}-{URUTAN}.

Mutasi tidak menghapus histori. Perubahan lokasi, kondisi, penguasa, dan penghapusan dicatat sebagai asset_mutations.

## Bansos / BLT-DD

Program memiliki kode, tahun, sumber dana, kuota, nominal per penerima dan status.

Penerima diikat ke data warga melalui citizen_id dan unik per program.

Alur penerima:

CALON → TERVERIFIKASI → DISALURKAN

atau

CALON → DITOLAK

Penyaluran menyimpan tanggal dan nomor referensi.

## Local-first

Asset, AssetMutation, AidProgram, dan AidRecipient menggunakan Syncable. Perubahan masuk ke sync_queue dan dapat direplikasi setelah koneksi tersedia.

## Integritas

Satu warga tidak boleh terdaftar dua kali pada program bantuan yang sama:

UNIQUE(aid_program_id, citizen_id)

Penghapusan data menggunakan soft delete pada master utama agar histori dapat dipertahankan.
