# 🏆 Web Aplikasi Prediksi Harga Emas

Aplikasi web berbasis Laravel untuk memprediksi harga emas menggunakan algoritma **Facebook Prophet** dengan integrasi Python. Data harga emas diambil otomatis dari Yahoo Finance dan dapat diprediksi hingga 10 tahun ke depan.

---

## 📋 Fitur Utama

### 1. **Dashboard**
- Menampilkan harga emas hari ini
- Perbandingan dengan harga bulan lalu
- Persentase perubahan harga
- Grafik tren 1 tahun terakhir
- Update data otomatis dengan satu klik

### 2. **Data Harga Emas**
- Menampilkan riwayat harga historis
- Filter berdasarkan rentang tanggal
- Visualisasi grafik interaktif
- Tambah data dari Yahoo Finance
- Konversi otomatis USD/oz ke IDR/gram

### 3. **Prediksi Harga**
- Prediksi 1-10 tahun ke depan
- Menggunakan model Prophet
- Menampilkan confidence interval (upper/lower bound)
- Grafik perbandingan aktual vs prediksi
- Metrik akurasi: MAPE, RMSE, MAE, R²

### 4. **Laporan**
- Ringkasan hasil prediksi
- Statistik harga (tertinggi, terendah, rata-rata)
- Grafik tren jangka panjang
- Export ke PDF
- Tabel perbandingan detail

### 5. **Autentikasi & Profil**
- Login/Logout dengan Laravel Breeze
- Manajemen profil pengguna
- Keamanan data dengan middleware

---

## 🛠 Teknologi yang Digunakan

### Backend
- **Laravel 11** - Framework PHP
- **MySQL/MariaDB** - Database
- **Laravel Breeze** - Authentication
- **DOMPDF** - PDF Generator

### Frontend
- **Blade Template** - Templating engine
- **Tailwind CSS** - Styling
- **Chart.js** - Visualisasi data
- **Alpine.js** - Interaktivitas

### Python & ML
- **Python 3.8+**
- **Prophet** - Time series forecasting
- **yfinance** - Data harga emas
- **Pandas** - Data manipulation
- **NumPy** - Komputasi numerik
- **scikit-learn** - Metrik evaluasi

---

## 📦 Persyaratan Sistem

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Python >= 3.8
- pip (Python package manager)

---

## 🚀 Cara Instalasi

### 1. Clone atau Download Project
```bash
git clone <repository-url>
cd prediksi-harga-emas
```

### 2. Install Dependencies Laravel
```bash
composer install
npm install
```

### 3. Install Dependencies Python
```bash
pip install -r requirements.txt
```

### 4. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_prediksi_emas
DB_USERNAME=root
DB_PASSWORD=

PYTHON_PATH=python
```

### 5. Buat Database
```bash
mysql -u root -p
CREATE DATABASE db_prediksi_emas;
EXIT;
```

### 6. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 7. Build Assets
```bash
npm run build
```

### 8. Update Data Historis
```bash
python python_scripts/update_data.py historical 5
```

### 9. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

**Login Default:**
- Email: `admin@example.com`
- Password: `password`

---

## 📊 Cara Menggunakan

### Update Data Harga Emas
1. Login ke aplikasi
2. Buka menu **Dashboard**
3. Klik tombol **"🔄 Update Harga"**
4. Data akan diperbarui otomatis dari Yahoo Finance

### Membuat Prediksi
1. Buka menu **Prediksi Harga**
2. Klik **"➕ Buat Prediksi Baru"**
3. Pilih periode prediksi (1-10 tahun)
4. Klik **"🚀 Mulai Prediksi"**
5. Tunggu proses selesai (beberapa menit)
6. Lihat hasil prediksi dan metrik akurasi

### Generate Laporan
1. Buka menu **Laporan**
2. Review ringkasan hasil prediksi
3. Klik **"📄 Download PDF"** untuk export

---

## 🔧 Konfigurasi Lanjutan

### Update Otomatis (Cron Job)
Untuk menjalankan update harga otomatis setiap hari:

**Linux/Mac:**
```bash
crontab -e
```
Tambahkan:
```
0 9 * * * cd /path/to/prediksi-harga-emas && php artisan schedule:run >> /dev/null 2>&1
```

**Windows (Task Scheduler):**
- Buat task baru
- Program: `php`
- Arguments: `artisan schedule:run`
- Working directory: path ke project

### Konfigurasi Python Path
Jika Python tidak terdeteksi, edit `.env`:
```env
# Windows
PYTHON_PATH=C:\Python39\python.exe

# Linux/Mac
PYTHON_PATH=/usr/bin/python3
```

---

## 📁 Struktur Project

```
prediksi-harga-emas/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── HargaEmasController.php
│   │   ├── PrediksiController.php
│   │   └── LaporanController.php
│   └── Models/
│       ├── HargaEmas.php
│       ├── Prediksi.php
│       └── Akurasi.php
├── database/
│   ├── migrations/
│   └── seeders/
├── python_scripts/
│   ├── update_data.py
│   ├── forecast_prophet.py
│   └── requirements.txt
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       ├── harga-emas/
│       ├── prediksi/
│       └── laporan/
├── routes/
│   └── web.php
├── .env
└── README.md
```

---

## 🧪 Testing

### Test Manual
1. **Test Update Data:**
   ```bash
   python python_scripts/update_data.py today
   ```

2. **Test Prediksi:**
   ```bash
   python python_scripts/forecast_prophet.py 5
   ```

3. **Test Database Connection:**
   ```bash
   php artisan tinker
   >>> \App\Models\HargaEmas::count()
   ```

---

## 🐛 Troubleshooting

### Error: "Python not found"
**Solusi:**
- Pastikan Python terinstall: `python --version`
- Update `PYTHON_PATH` di `.env`

### Error: "Prophet installation failed"
**Solusi:**
- Windows: Install Visual C++ Build Tools
- Linux: `sudo apt-get install build-essential`
- Mac: `xcode-select --install`

### Error: "Database connection refused"
**Solusi:**
- Cek MySQL service: `sudo service mysql status`
- Verifikasi kredensial di `.env`
- Test connection: `mysql -u root -p`

### Error: "Class not found"
**Solusi:**
```bash
composer dump-autoload
php artisan config:clear
```

---

## 📈 Metrik Akurasi Model

Model Prophet menghasilkan 4 metrik utama:

1. **MAPE** (Mean Absolute Percentage Error)
   - Semakin kecil semakin baik
   - < 10% = Excellent
   - 10-20% = Good
   - > 20% = Perlu perbaikan

2. **RMSE** (Root Mean Square Error)
   - Mengukur rata-rata error dalam satuan asli
   - Sensitif terhadap outlier

3. **MAE** (Mean Absolute Error)
   - Rata-rata error absolut
   - Lebih robust terhadap outlier

4. **R²** (Coefficient of Determination)
   - 0 - 1 (semakin tinggi semakin baik)
   - > 0.9 = Excellent fit
   - 0.7-0.9 = Good fit
   - < 0.7 = Poor fit

---

## 🔐 Keamanan

- Autentikasi dengan Laravel Breeze
- Password hashing dengan bcrypt
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)

---

## 📝 Lisensi

Project ini untuk keperluan edukasi dan pembelajaran.

---

## 👨‍💻 Author

Dibuat dengan ❤️ menggunakan Laravel & Python Prophet

---

## 📞 Support

Jika ada pertanyaan atau kendala:
1. Cek dokumentasi Laravel: https://laravel.com/docs
2. Cek dokumentasi Prophet: https://facebook.github.io/prophet/
3. Baca troubleshooting guide di atas

---

## 🎯 Roadmap Future Features

- [ ] API untuk integrasi dengan aplikasi mobile
- [ ] Multi-currency support
- [ ] Real-time notification
- [ ] Advanced analytics dashboard
- [ ] Export ke Excel/CSV
- [ ] Comparison dengan model lain (ARIMA, LSTM)
- [ ] Multi-language support

---

**Happy Coding! 🚀**
# 🏆 Web Aplikasi Prediksi Harga Emas

Aplikasi web berbasis Laravel untuk memprediksi harga emas menggunakan algoritma **Facebook Prophet** dengan integrasi Python. Data harga emas diambil otomatis dari Yahoo Finance dan dapat diprediksi hingga 10 tahun ke depan.

---

## 📋 Fitur Utama

### 1. **Dashboard**
- Menampilkan harga emas hari ini
- Perbandingan dengan harga bulan lalu
- Persentase perubahan harga
- Grafik tren 1 tahun terakhir
- Update data otomatis dengan satu klik

### 2. **Data Harga Emas**
- Menampilkan riwayat harga historis
- Filter berdasarkan rentang tanggal
- Visualisasi grafik interaktif
- Tambah data dari Yahoo Finance
- Konversi otomatis USD/oz ke IDR/gram

### 3. **Prediksi Harga**
- Prediksi 1-10 tahun ke depan
- Menggunakan model Prophet
- Menampilkan confidence interval (upper/lower bound)
- Grafik perbandingan aktual vs prediksi
- Metrik akurasi: MAPE, RMSE, MAE, R²

### 4. **Laporan**
- Ringkasan hasil prediksi
- Statistik harga (tertinggi, terendah, rata-rata)
- Grafik tren jangka panjang
- Export ke PDF
- Tabel perbandingan detail

### 5. **Autentikasi & Profil**
- Login/Logout dengan Laravel Breeze
- Manajemen profil pengguna
- Keamanan data dengan middleware

---

## 🛠 Teknologi yang Digunakan

### Backend
- **Laravel 11** - Framework PHP
- **MySQL/MariaDB** - Database
- **Laravel Breeze** - Authentication
- **DOMPDF** - PDF Generator

### Frontend
- **Blade Template** - Templating engine
- **Tailwind CSS** - Styling
- **Chart.js** - Visualisasi data
- **Alpine.js** - Interaktivitas

### Python & ML
- **Python 3.8+**
- **Prophet** - Time series forecasting
- **yfinance** - Data harga emas
- **Pandas** - Data manipulation
- **NumPy** - Komputasi numerik
- **scikit-learn** - Metrik evaluasi

---

## 📦 Persyaratan Sistem

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Python >= 3.8
- pip (Python package manager)

---

## 🚀 Cara Instalasi

### 1. Clone atau Download Project
```bash
git clone <repository-url>
cd prediksi-harga-emas
```

### 2. Install Dependencies Laravel
```bash
composer install
npm install
```

### 3. Install Dependencies Python
```bash
pip install -r requirements.txt
```

### 4. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_prediksi_emas
DB_USERNAME=root
DB_PASSWORD=

PYTHON_PATH=python
```

### 5. Buat Database
```bash
mysql -u root -p
CREATE DATABASE db_prediksi_emas;
EXIT;
```

### 6. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

### 7. Build Assets
```bash
npm run build
```

### 8. Update Data Historis
```bash
python python_scripts/update_data.py historical 5
```

### 9. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

**Login Default:**
- Email: `admin@example.com`
- Password: `password`

---

## 📊 Cara Menggunakan

### Update Data Harga Emas
1. Login ke aplikasi
2. Buka menu **Dashboard**
3. Klik tombol **"🔄 Update Harga"**
4. Data akan diperbarui otomatis dari Yahoo Finance

### Membuat Prediksi
1. Buka menu **Prediksi Harga**
2. Klik **"➕ Buat Prediksi Baru"**
3. Pilih periode prediksi (1-10 tahun)
4. Klik **"🚀 Mulai Prediksi"**
5. Tunggu proses selesai (beberapa menit)
6. Lihat hasil prediksi dan metrik akurasi

### Generate Laporan
1. Buka menu **Laporan**
2. Review ringkasan hasil prediksi
3. Klik **"📄 Download PDF"** untuk export

---

## 🔧 Konfigurasi Lanjutan

### Update Otomatis (Cron Job)
Untuk menjalankan update harga otomatis setiap hari:

**Linux/Mac:**
```bash
crontab -e
```
Tambahkan:
```
0 9 * * * cd /path/to/prediksi-harga-emas && php artisan schedule:run >> /dev/null 2>&1
```

**Windows (Task Scheduler):**
- Buat task baru
- Program: `php`
- Arguments: `artisan schedule:run`
- Working directory: path ke project

### Konfigurasi Python Path
Jika Python tidak terdeteksi, edit `.env`:
```env
# Windows
PYTHON_PATH=C:\Python39\python.exe

# Linux/Mac
PYTHON_PATH=/usr/bin/python3
```

---

## 📁 Struktur Project

```
prediksi-harga-emas/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── HargaEmasController.php
│   │   ├── PrediksiController.php
│   │   └── LaporanController.php
│   └── Models/
│       ├── HargaEmas.php
│       ├── Prediksi.php
│       └── Akurasi.php
├── database/
│   ├── migrations/
│   └── seeders/
├── python_scripts/
│   ├── update_data.py
│   ├── forecast_prophet.py
│   └── requirements.txt
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       ├── harga-emas/
│       ├── prediksi/
│       └── laporan/
├── routes/
│   └── web.php
├── .env
└── README.md
```

---

## 🧪 Testing

### Test Manual
1. **Test Update Data:**
   ```bash
   python python_scripts/update_data.py today
   ```

2. **Test Prediksi:**
   ```bash
   python python_scripts/forecast_prophet.py 5
   ```

3. **Test Database Connection:**
   ```bash
   php artisan tinker
   >>> \App\Models\HargaEmas::count()
   ```

---

## 🐛 Troubleshooting

### Error: "Python not found"
**Solusi:**
- Pastikan Python terinstall: `python --version`
- Update `PYTHON_PATH` di `.env`

### Error: "Prophet installation failed"
**Solusi:**
- Windows: Install Visual C++ Build Tools
- Linux: `sudo apt-get install build-essential`
- Mac: `xcode-select --install`

### Error: "Database connection refused"
**Solusi:**
- Cek MySQL service: `sudo service mysql status`
- Verifikasi kredensial di `.env`
- Test connection: `mysql -u root -p`

### Error: "Class not found"
**Solusi:**
```bash
composer dump-autoload
php artisan config:clear
```

---

## 📈 Metrik Akurasi Model

Model Prophet menghasilkan 4 metrik utama:

1. **MAPE** (Mean Absolute Percentage Error)
   - Semakin kecil semakin baik
   - < 10% = Excellent
   - 10-20% = Good
   - > 20% = Perlu perbaikan

2. **RMSE** (Root Mean Square Error)
   - Mengukur rata-rata error dalam satuan asli
   - Sensitif terhadap outlier

3. **MAE** (Mean Absolute Error)
   - Rata-rata error absolut
   - Lebih robust terhadap outlier

4. **R²** (Coefficient of Determination)
   - 0 - 1 (semakin tinggi semakin baik)
   - > 0.9 = Excellent fit
   - 0.7-0.9 = Good fit
   - < 0.7 = Poor fit

---

## 🔐 Keamanan

- Autentikasi dengan Laravel Breeze
- Password hashing dengan bcrypt
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)

---

## 📝 Lisensi

Project ini untuk keperluan edukasi dan pembelajaran.

---

## 👨‍💻 Author

Dibuat dengan ❤️ menggunakan Laravel & Python Prophet

---

## 📞 Support

Jika ada pertanyaan atau kendala:
1. Cek dokumentasi Laravel: https://laravel.com/docs
2. Cek dokumentasi Prophet: https://facebook.github.io/prophet/
3. Baca troubleshooting guide di atas

---

## 🎯 Roadmap Future Features

- [ ] API untuk integrasi dengan aplikasi mobile
- [ ] Multi-currency support
- [ ] Real-time notification
- [ ] Advanced analytics dashboard
- [ ] Export ke Excel/CSV
- [ ] Comparison dengan model lain (ARIMA, LSTM)
- [ ] Multi-language support

---

**Happy Coding! 🚀**
