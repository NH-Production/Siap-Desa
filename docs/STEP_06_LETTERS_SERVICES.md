# STEP 06 — Persuratan & Pelayanan Publik

## Surat bawaan

SKTM, SKU, SKDOM, pengantar SKCK, surat kelahiran, dan surat kematian.

## Mesin template

Jenis surat disimpan pada letter_types. Format nomor menggunakan placeholder:

- {KODE}
- {NO}
- {BULAN_ROMAWI}
- {TAHUN}

Field tambahan dapat disimpan pada letter_data sehingga template dapat dikembangkan tanpa mengubah struktur inti.

## Alur

DRAFT -> SUBMITTED -> APPROVED / REJECTED / CANCELLED

Saat APPROVED, sistem membuat nomor resmi secara lokal dan menyimpan waktu serta pejabat penyetuju.

## QR verifikasi

Setiap surat mempunyai qr_verification_token. QR dicetak pada surat. Token tidak menggunakan NIK.

Untuk verifikasi publik tahap berikutnya akan tersedia endpoint yang hanya menampilkan metadata surat yang aman, tanpa membuka data pribadi yang tidak diperlukan.

## Pelayanan

Service request mendukung SUBMITTED, VERIFIED, PROCESSING, COMPLETED, REJECTED, dan CANCELLED.

Semua perubahan operasional menggunakan Syncable dan masuk ke sync_queue.
