# Deployment Guide - Laporin (CodeIgniter 4 + MySQL)

Ringkasan langkah untuk men-deploy aplikasi ke server produksi.

## 1. Persiapan Server
- Siapkan server dengan PHP 8.0+ (sesuai requirement CodeIgniter 4), MySQL/MariaDB, dan Composer.
- Install ekstensi PHP penting: `pdo`, `pdo_mysql`, `mbstring`, `intl`, `json`, `fileinfo`, `openssl`.

Untuk development cepat di Windows gunakan Laragon atau XAMPP. Untuk production gunakan Ubuntu/Debian atau CentOS.

## 2. Clone & Composer
```bash
cd /var/www
git clone <repo-url> laporin
cd laporin
composer install --no-dev --optimize-autoloader
```

## 3. Environment (.env)
- Copy file `.env.example` ke `.env` dan atur variabel:
  - `app.baseURL` — set ke URL publik (https://yourdomain.com)
  - `database.default.hostname`, `database.default.database`, `database.default.username`, `database.default.password`
  - `session.saveHandler` (file/database) sesuai kebutuhan
  - `app.environment = production`

Jangan commit file `.env` ke Git.

## 4. Migrate & Seed Database
Jika Anda menggunakan migration/seeder bawaan:
```bash
php spark migrate --all
php spark db:seed InitialSeeder
```
Jika tidak ada spark commands, jalankan SQL migrations manual.

## 5. Permissions
Set folder writable untuk `writable/` dan `public/uploads`:
```bash
chown -R www-data:www-data /var/www/laporin
chmod -R 775 /var/www/laporin/writable
chmod -R 775 /var/www/laporin/public/uploads
```

## 6. Webserver (Nginx/Apache)
- Nginx: point `root` ke `/var/www/laporin/public` dan atur `try_files $uri $uri/ /index.php?$query_string`.
- Apache: aktifkan `mod_rewrite` dan pastikan `DocumentRoot` menunjuk ke `public/`.

Contoh Nginx server block:
```nginx
server {
  listen 80;
  server_name yourdomain.com;
  root /var/www/laporin/public;
  index index.php;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }
}
```

## 7. SSL (Let's Encrypt)
Gunakan Certbot untuk mengeluarkan sertifikat SSL dan redirect HTTP -> HTTPS.

## 8. Queue / Background (Opsional)
Jika menggunakan job queue, siapkan supervisord atau systemd service untuk worker.

## 9. Backup & Maintenance
- Buat backup rutin (dump database, backup `uploads/`): gunakan cron.
- Contoh cron daily backup:
```bash
0 2 * * * /usr/bin/mysqldump -u root -pPASSWORD dbname | gzip > /backups/db_$(date +\%F).sql.gz
```

## 10. Logging & Monitoring
- Pastikan `writable/logs` dapat ditulis dan rotasi log tersedia.
- Aktifkan monitoring (Prometheus/Grafana) atau gunakan server monitoring sederhana.

## 11. Composer & Assets
- Jika ada asset build step (npm/webpack), jalankan build dan simpan hasil di `public/`.

## 12. Dompdf & PHP extensions
- Untuk export PDF install `dompdf/dompdf` via Composer:
```bash
composer require dompdf/dompdf
```
- Pastikan `ext-gd` dan `ext-mbstring` aktif.

## 13. After Deploy Checklist
- Cek koneksi DB
- Cek folder `writable/` dan `public/uploads`
- Jalankan smoke test: buka `/`, login, buka `/admin/analytics`

## 14. Rollback
- Simpan backup DB sebelum migrasi bila perlu. Gunakan struktur versioned migrations untuk rollback.

---
Jika Anda mau, saya bisa menambahkan skrip deploy otomatis (Capistrano/Envoy) dan sample `systemd` service untuk queue.
