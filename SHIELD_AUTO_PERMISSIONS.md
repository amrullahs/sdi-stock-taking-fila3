# Shield Auto-Generate Permissions

Sistem ini secara otomatis menggenerate permissions untuk resource Filament baru menggunakan package `bezhanSalleh/filament-shield`.

## Komponen yang Dibuat

### 1. ShieldAutoGenerateProvider
- **File**: `app/Providers/ShieldAutoGenerateProvider.php`
- **Fungsi**: Service provider yang mendaftarkan command dan event listener
- **Event**: Mendengarkan `artisan.command.finished` untuk command `make:filament-resource`

### 2. ShieldAutoGenerate Command
- **File**: `app/Console/Commands/ShieldAutoGenerate.php`
- **Command**: `php artisan shield:auto-generate`
- **Fungsi**: 
  - Mendeteksi resource baru dengan membandingkan file saat ini dengan cache
  - Menjalankan `shield:generate --all` jika ada resource baru
  - Menyimpan cache untuk tracking resource

### 3. Konfigurasi Shield
- **File**: `config/filament-shield.php`
- **Perubahan**: 
  - `discover_all_resources` = `true`
  - `discover_all_widgets` = `true`
  - `discover_all_pages` = `true`

## Cara Kerja

1. **Otomatis**: Ketika menjalankan `php artisan make:filament-resource`, sistem akan otomatis generate permissions
2. **Manual**: Jalankan `php artisan shield:auto-generate` untuk generate permissions secara manual
3. **Force**: Gunakan `php artisan shield:auto-generate --force` untuk regenerate semua permissions

## Cache System

- **File Cache**: `storage/framework/cache/shield_resources_cache.json`
- **Fungsi**: Menyimpan daftar resource yang sudah diproses untuk menghindari regenerasi yang tidak perlu

## Logging

Semua aktivitas auto-generation dicatat di log Laravel:
- Info: Resource baru terdeteksi
- Info: Permissions berhasil digenerate
- Error: Jika terjadi kesalahan

## Testing

Untuk menguji sistem:
1. Buat resource baru: `php artisan make:filament-resource TestResource`
2. Periksa log untuk konfirmasi auto-generation
3. Verifikasi permissions di database atau Filament Shield panel

## Troubleshooting

- Jika auto-generation tidak bekerja, jalankan manual: `php artisan shield:auto-generate --force`
- Periksa log Laravel untuk error messages
- Pastikan Shield package sudah dikonfigurasi dengan benar