# SDI Stock Taking Application

Aplikasi internal stock taking menggunakan Laravel 11 dengan Filament 3.3 untuk PT Sankei Dharma Indonesia.

## Fitur Utama

- **Dashboard Admin**: Interface modern dengan Filament 3.3
- **Master Data Management**: Line, Product Structure, Model Structure
- **Stock Taking Process**: Period STO, Line STO, Stock Taking Detail
- **User Management**: Role-based access dengan Filament Shield
- **Import/Export**: CSV import untuk master data dan Excel export untuk Line STO Detail
- **Activity Log**: Pencatatan aktivitas login/logout dan perubahan data
- **Modal Relation Manager**: Interface yang user-friendly

## Tech Stack

- **Backend**: Laravel 11
- **Frontend**: Filament 3.3
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Permission + Filament Shield
- **UI Components**: Tailwind CSS

## Key Packages

- **bezhansalleh/filament-shield**: Role & Permission management
- **spatie/laravel-permission**: Permission system
- **spatie/laravel-activitylog**: Activity logging system
- **rmsramos/filament-activitylog**: Filament plugin untuk activity log
- **pxlrbt/filament-excel**: Export functionality untuk Excel
- **guava/filament-modal-relation-managers**: Modal relation managers
- **awcodes/filament-sticky-header**: Sticky header untuk tables
- **maatwebsite/excel**: Import/Export Excel/CSV
- **filament/filament**: Admin panel framework

## Instalasi

### Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer 2.x
- MySQL 8.0 atau lebih tinggi
- Node.js 18+ dan NPM
- Web server (Apache/Nginx untuk production)
- Git

## Instalasi Development

### Quick Start (Development)

```bash
# Clone repository
git clone https://github.com/your-repo/sdi-stock-taking-fila3.git
cd sdi-stock-taking-fila3

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env untuk database development
# DB_DATABASE=sdi_stock_taking_dev
# DB_USERNAME=root
# DB_PASSWORD=

# Setup database
php artisan migrate:fresh --seed
php artisan shield:auto-generate --force
php artisan db:seed --class=SuperAdminSeeder
php artisan cache:clear

# Start development server
php artisan serve
# Akses: http://localhost:8000/admin
```

## Instalasi Production Server

### Step 1: Clone Repository

```bash
# Clone repository
git clone https://github.com/your-repo/sdi-stock-taking-fila3.git
cd sdi-stock-taking-fila3

# Set permissions (Linux/Unix)
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies
npm install

# Build assets untuk production
npm run build
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit file `.env` dengan konfigurasi production:

```env
# Application
APP_NAME="SDI Stock Taking"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sdi_stock_taking
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Mail (optional)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 4: Database Setup

```bash
# Create database (MySQL)
mysql -u root -p
CREATE DATABASE sdi_stock_taking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sdi_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON sdi_stock_taking.* TO 'sdi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations and seeders
php artisan migrate:fresh --seed

# Generate Filament Shield permissions (WAJIB setelah migrate:fresh)
php artisan shield:auto-generate --force

# Re-seed Super Admin untuk assign permissions yang baru dibuat
php artisan db:seed --class=SuperAdminSeeder

# Clear cache untuk apply perubahan permissions
php artisan cache:clear
```

### Step 5: Storage & Cache Optimization

```bash
# Create storage link
php artisan storage:link

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 6: Web Server Configuration

#### Apache (.htaccess)

Pastikan mod_rewrite aktif dan DocumentRoot mengarah ke folder `public/`:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/sdi-stock-taking-fila3/public
    
    <Directory /path/to/sdi-stock-taking-fila3/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/sdi-stock-taking_error.log
    CustomLog ${APACHE_LOG_DIR}/sdi-stock-taking_access.log combined
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/sdi-stock-taking-fila3/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Step 7: SSL Certificate (Recommended)

```bash
# Install Certbot (Ubuntu/Debian)
sudo apt install certbot python3-certbot-apache

# Generate SSL certificate
sudo certbot --apache -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Step 8: Monitoring & Maintenance

```bash
# Setup log rotation
sudo nano /etc/logrotate.d/laravel
```

Isi file logrotate:

```
/path/to/sdi-stock-taking-fila3/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
```

## Default User

Setelah instalasi, login dengan:

- **URL**: `https://your-domain.com/admin` (production) atau `http://localhost:8000/admin` (development)
- **Email**: `amrullah@sankei-dharma.com`
- **Password**: `password`
- **Role**: Super Admin (168+ permissions)

⚠️ **PENTING**: Segera ganti password default setelah login pertama!

## Struktur Database

### Master Tables
- `m_line` - Data line produksi
- `m_model_structure` - Struktur model produk
- `m_model_structure_detail` - Detail struktur model
- `m_line_model_detail` - Relasi line dengan model detail
- `index_product_structure` - Struktur produk utama

### Transaction Tables
- `t_period_sto` - Periode stock taking
- `t_line_sto` - Line stock taking per periode
- `t_line_sto_detail` - Detail stock taking per line
- `t_stock_on_hand` - Data stock on hand
- `t_stock_taking` - Header stock taking
- `t_stock_taking_detail` - Detail stock taking

### System Tables
- `users` - User management
- `roles` - Role management (Spatie Permission)
- `permissions` - Permission management (Spatie Permission)
- `model_has_roles` - User-role assignment
- `role_has_permissions` - Role-permission assignment
- `activity_log` - Activity logging (Spatie Activity Log)

## Activity Log

Aplikasi dilengkapi dengan sistem activity log yang mencatat:

### Aktivitas yang Dicatat
- **Login/Logout Events**: Pencatatan waktu login dan logout user dengan IP address dan user agent
- **Data Changes**: Perubahan pada model PeriodSto, LineSto, dan LineStoDetail
- **User Actions**: Aktivitas user dalam sistem

### Akses Activity Log
1. Login sebagai user dengan permission `view_activity_log`
2. Navigasi ke menu "Log Activity" di sidebar
3. Filter berdasarkan log name, user, atau tanggal

### Permission Required
- `view_activity_log`: Untuk melihat activity log
- Secara default, hanya role `super_admin` yang memiliki akses

### Konfigurasi
- File konfigurasi: `config/activitylog.php`
- Plugin Filament: `config/filament-activitylog.php`
- Service Provider: Terdaftar di `bootstrap/providers.php`

## Available Seeders

- **UserSeeder**: Membuat user dummy untuk testing
- **RolePermissionSeeder**: Membuat roles (super-admin, Stock Taker, Viewer)
- **SuperAdminSeeder**: Membuat user super-admin dengan semua permissions
- **ProductStructureSeeder**: Seeder master data produk (opsional)
- **ModelStructureSeeder**: Seeder master data model (opsional)
- **ModelStructureDetailSeeder**: Seeder detail model (opsional)

## Import Master Data

Setelah instalasi, import master data melalui admin panel:

1. **Product Structure**: Import dari CSV
2. **Model Structure**: Import dari CSV
3. **Line Model Detail**: Import dari CSV
4. **Stock On Hand**: Import dari CSV

## Backup Database

```bash
# Backup harian
mysqldump -u sdi_user -p sdi_stock_taking > backup_$(date +%Y%m%d).sql

# Restore
mysql -u sdi_user -p sdi_stock_taking < backup_20250120.sql
```

## Update Deployment ke Server Production

Panduan untuk melakukan update aplikasi di server production:

### Step 1: Backup Sebelum Update

```bash
# Backup database
mysqldump -u sdi_user -p sdi_stock_taking > backup_before_update_$(date +%Y%m%d_%H%M).sql

# Backup aplikasi
cp -r /path/to/sdi-stock-taking-fila3 /path/to/backup/sdi-stock-taking-fila3_$(date +%Y%m%d_%H%M)
```

### Step 2: Pull Latest Changes

```bash
# Masuk ke direktori aplikasi
cd /path/to/sdi-stock-taking-fila3

# Stash perubahan lokal (jika ada)
git stash

# Pull commit terbaru
git pull origin main

# Atau pull commit tertentu
git fetch
git checkout <commit-hash>
```

### Step 3: Update Dependencies

```bash
# Update PHP dependencies
composer install --optimize-autoloader --no-dev

# Update Node.js dependencies (jika ada perubahan)
npm install

# Build assets untuk production
npm run build
```

### Step 4: Database Migration

```bash
# Jalankan migration baru (jika ada)
php artisan migrate --force

# Jika ada perubahan permission/role
php artisan shield:auto-generate --force

# Re-assign permissions ke super admin
php artisan db:seed --class=SuperAdminSeeder
```

### Step 5: Clear Cache & Optimize

```bash
# Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache ulang untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### Step 6: Restart Services

```bash
# Restart PHP-FPM (jika menggunakan Nginx)
sudo systemctl restart php8.2-fpm

# Restart Apache (jika menggunakan Apache)
sudo systemctl restart apache2

# Restart Nginx (jika menggunakan Nginx)
sudo systemctl restart nginx
```

### Step 7: Verifikasi Update

1. **Test Login**: Pastikan login masih berfungsi
2. **Check Activity Log**: Verifikasi menu "Log Activity" muncul untuk super admin
3. **Test Export**: Coba export Excel dari Line STO Detail
4. **Check Permissions**: Pastikan semua permission masih berfungsi
5. **Monitor Logs**: Periksa `storage/logs/laravel.log` untuk error

### Rollback (Jika Diperlukan)

Jika terjadi masalah setelah update:

```bash
# Rollback ke commit sebelumnya
git checkout <previous-commit-hash>

# Restore database
mysql -u sdi_user -p sdi_stock_taking < backup_before_update_YYYYMMDD_HHMM.sql

# Clear cache
php artisan cache:clear
php artisan config:clear

# Restart services
sudo systemctl restart php8.2-fpm nginx
```

### Checklist Update

- [ ] Backup database dan aplikasi
- [ ] Pull latest changes dari repository
- [ ] Update dependencies (composer & npm)
- [ ] Run database migrations
- [ ] Update permissions dengan Shield
- [ ] Clear dan cache ulang
- [ ] Restart web services
- [ ] Test fungsionalitas utama
- [ ] Monitor logs untuk error

### Fitur Baru dalam Update Ini

- ✅ **Activity Log System**: Pencatatan aktivitas login/logout dan perubahan data
- ✅ **Excel Export**: Export data Line STO Detail ke format Excel
- ✅ **Enhanced Security**: Improved permission management
- ✅ **Better UX**: Modal-based export functionality

## Troubleshooting

### Permission Issues

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Filament Shield Issues

**Problem**: User super-admin hanya bisa akses "User Management" setelah fresh install

**Solution**: Pastikan urutan perintah berikut dijalankan dengan benar:

```bash
# 1. Fresh migration dan seeding
php artisan migrate:fresh --seed

# 2. Generate permissions untuk semua resources
php artisan shield:auto-generate --force

# 3. Re-assign permissions ke super-admin
php artisan db:seed --class=SuperAdminSeeder

# 4. Clear cache
php artisan cache:clear
```

**Verifikasi**: User super-admin harus memiliki 168+ permissions setelah proses di atas.

### Activity Log Issues

**Problem**: Error "Class 'Spatie\Activitylog\Facades\LogActivity' not found"

**Solution**: Pastikan ActivitylogServiceProvider terdaftar di `bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\ShieldAutoGenerateProvider::class,
    Spatie\Activitylog\ActivitylogServiceProvider::class, // Pastikan ini ada
];
```

Kemudian clear cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**Problem**: Menu "Log Activity" tidak muncul di sidebar

**Solution**: 
1. Pastikan permission `view_activity_log` sudah dibuat
2. Assign permission ke role yang sesuai
3. Clear permission cache

```bash
# Buat permission jika belum ada
php artisan tinker
>>> Spatie\Permission\Models\Permission::create(['name' => 'view_activity_log']);

# Assign ke super admin role
>>> $role = Spatie\Permission\Models\Role::findByName('super_admin');
>>> $role->givePermissionTo('view_activity_log');

# Clear cache
php artisan permission:cache-reset
```

### Database Connection Error

1. Periksa kredensial database di `.env`
2. Pastikan MySQL service berjalan
3. Test koneksi: `php artisan tinker` → `DB::connection()->getPdo()`

### 500 Internal Server Error

1. Periksa log: `tail -f storage/logs/laravel.log`
2. Pastikan permissions folder storage dan bootstrap/cache
3. Periksa konfigurasi web server

## Support

Untuk bantuan teknis, hubungi tim IT PT Sankei Dharma Indonesia.

## License

Proprietari - PT Sankei Dharma Indonesia
