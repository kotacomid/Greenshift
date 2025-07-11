# Z-Library Automation Tool

Aplikasi otomatis untuk mengelola workflow Z-Library yang mencakup pencarian, download, upload ke Google Drive, dan pembuatan katalog HTML.

## 🚀 Fitur Utama

### Workflow Lengkap:
1. **Login dan Search** - Otentikasi ke Z-Library dan pencarian buku
2. **Store Metadata di CSV** - Simpan metadata buku yang dipilih 
3. **Download File & Cover** - Download buku dan cover dengan nama konsisten (max 160 karakter)
4. **Upload ke Google Drive** - Upload file dan cover ke Drive, dapatkan link
5. **Generate HTML Catalog** - Buat katalog HTML dengan link Drive dan preview cover

### Fitur Tambahan:
- ✅ **Cover Download & Upload** - Download dan upload cover buku otomatis
- ✅ **Smart File Naming** - Nama file "Judul - Author" max 160 karakter, Windows compatible
- ✅ **Web Dashboard** - Interface web modern untuk mengelola koleksi
- ✅ **Command Line Interface** - Untuk automasi dan scripting
- ✅ **Google Drive Integration** - Upload otomatis book & cover dengan link sharing
- ✅ **HTML Catalog Generator** - Katalog responsif dengan cover preview dan filter
- ✅ **CSV Data Management** - Tracking status dan metadata lengkap
- ✅ **Cross-Platform Support** - Windows, Linux, macOS compatible
- ✅ **cPanel Deployment Ready** - Siap deploy di hosting shared

## 📋 Requirements

- Python 3.7+
- Z-Library account (email & password)
- Google Drive API credentials (opsional)
- Windows 10+, Linux, atau macOS
- cPanel hosting dengan Python support (untuk deployment)

## 🛠️ Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd z-library-automation
```

### 2. Install Dependencies
```bash
pip install -r requirements.txt
```

### 3. Konfigurasi

#### Setup Environment Variables
```bash
# Copy template .env
cp .env.example .env

# Edit .env dengan credentials Anda
nano .env
```

Isi file `.env`:
```env
# Z-Library Credentials
ZLIBRARY_EMAIL=your-email@example.com
ZLIBRARY_PASSWORD=your-password

# Google Drive API Settings (opsional)
GOOGLE_CREDENTIALS_FILE=credentials.json
GOOGLE_TOKEN_FILE=token.json
DRIVE_FOLDER_ID=your-google-drive-folder-id

# File Paths
CSV_FILE_PATH=zlibrary_books.csv
HTML_OUTPUT_PATH=book_catalog.html
DOWNLOAD_PATH=downloads/

# Flask Settings
FLASK_SECRET_KEY=your-secret-key-here
FLASK_DEBUG=False
FLASK_HOST=0.0.0.0
FLASK_PORT=5000
```

#### Setup Google Drive API (Opsional)
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih existing project
3. Enable Google Drive API
4. Buat Service Account atau OAuth 2.0 credentials
5. Download `credentials.json` dan simpan di root directory
6. Untuk folder ID, buka Google Drive → klik kanan folder → Get link → copy ID dari URL

## 🚀 Penggunaan

### Command Line Interface

#### Mode Interaktif
```bash
python main.py --interactive
```

#### Workflow Lengkap
```bash
# Jalankan semua step untuk query tertentu
python main.py --query "Python programming" --count 10
```

#### Step Individual
```bash
# Hanya download pending books
python main.py --download-only

# Hanya upload ke Drive
python main.py --upload-only

# Hanya generate HTML
python main.py --html-only
```

#### Help
```bash
python main.py --help
```

### Web Interface

#### Development
```bash
python app.py
```
Akses: `http://localhost:5000`

#### Production (cPanel)
1. Upload semua file ke public_html atau subdirectory
2. Pastikan `app.py` sebagai entry point
3. Setup `.htaccess` untuk WSGI

### Contoh .htaccess untuk cPanel:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ app.py [QSA,L]
```

## 📁 Struktur File

```
z-library-automation/
├── main.py                 # CLI application
├── app.py                  # Web application
├── config.py               # Configuration management
├── zlibrary_manager.py     # Z-Library operations
├── csv_manager.py          # CSV data management
├── drive_manager.py        # Google Drive operations
├── html_generator.py       # HTML catalog generator
├── requirements.txt        # Python dependencies
├── .env.example           # Environment template
├── README.md              # This file
├── credentials.json       # Google Drive credentials (create this)
├── token.json            # Google Drive token (auto-generated)
├── zlibrary_books.csv    # Book metadata (auto-created)
├── book_catalog.html     # Generated catalog (auto-created)
├── stats.html           # Statistics page (auto-created)
└── downloads/           # Downloaded books (auto-created)
```

## 🎯 Workflow Detail

### 1. Search & Store Metadata
- Login ke Z-Library menggunakan credentials
- Search buku berdasarkan query
- Parse metadata (title, authors, year, publisher, dll.)
- Simpan ke CSV dengan status 'pending'

### 2. Download Books & Covers
- Ambil buku dengan status 'pending' dari CSV
- Download file buku dari Z-Library dengan nama "Judul - Author.ext" (max 160 karakter)
- Download cover buku dengan nama yang sama + ".jpg"
- Nama file Windows-compatible (tanpa karakter invalid)
- Update status ke 'completed' dan simpan local path
- Handle error dan update status ke 'error' jika gagal

### 3. Upload to Google Drive
- Ambil buku dengan status 'completed' yang belum di-upload
- Upload file buku dan cover ke Google Drive folder
- Set permission public dan dapatkan shareable link untuk keduanya
- Update CSV dengan Drive link buku dan cover

### 4. Generate HTML Catalog
- Baca semua data dari CSV
- Generate HTML catalog responsif dengan:
  - Search dan filter functionality
  - Statistics dashboard
  - Book grid dengan cover preview dari Google Drive
  - Separate links untuk book dan cover di Drive
  - Z-Library links
  - Mobile-responsive design

## 🌐 Web Dashboard Features

### Dashboard Utama
- **System Status** - Status koneksi Z-Library dan Google Drive
- **Statistics Cards** - Total books, downloaded, in Drive
- **Search Form** - Cari dan tambah buku baru
- **Quick Actions** - Download, upload, generate HTML
- **Recent Books** - Tabel buku terbaru

### Halaman Books
- **Complete Book List** - Semua buku dengan pagination
- **Status Indicators** - Visual status setiap buku
- **Direct Links** - Link ke Google Drive dan Z-Library

### Halaman Statistics
- **Collection Overview** - Statistik lengkap koleksi
- **Status Distribution** - Breakdown berdasarkan status
- **Language Distribution** - Breakdown berdasarkan bahasa
- **Format Distribution** - Breakdown berdasarkan format file

## ⚙️ Konfigurasi Lanjutan

### Google Drive Folder Structure
Buat struktur folder di Google Drive:
```
Z-Library Books/
├── Programming/
├── Science/
├── Literature/
└── Other/
```

### CSV Schema
File CSV memiliki kolom:
- `id`, `title`, `authors`, `year`, `publisher`
- `language`, `extension`, `size`, `rating`
- `url`, `cover`, `isbn`, `search_query`
- `download_status`, `download_url`, `local_path`
- `drive_link`, `cover_local_path`, `cover_drive_link`
- `added_date`, `updated_date`

### Status Values
- `pending` - Belum di-download
- `downloading` - Sedang di-download
- `completed` - Sudah di-download
- `error` - Error saat download

## 🔧 Troubleshooting

### Z-Library Authentication Issues
```bash
# Test login manual
python -c "
import asyncio
from zlibrary_manager import ZLibraryManager
async def test():
    manager = ZLibraryManager()
    result = await manager.login('email@example.com', 'password')
    print('Success:', result)
asyncio.run(test())
"
```

### Google Drive Permission Issues
1. Pastikan credentials.json valid
2. Check Google Drive API quota
3. Verify folder permissions

### cPanel Deployment Issues
1. Pastikan Python version compatibility
2. Check file permissions (755 untuk directories, 644 untuk files)
3. Verify WSGI configuration

## 📝 Development

### Adding New Features
1. Extend appropriate manager class
2. Add CLI arguments in `main.py`
3. Add web routes in `app.py`
4. Update HTML templates if needed

### Testing
```bash
# Test individual components
python zlibrary_manager.py
python csv_manager.py
python drive_manager.py
python html_generator.py
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch
3. Make changes
4. Test thoroughly
5. Submit pull request

## 📄 License

MIT License - lihat file LICENSE untuk detail.

## ⚠️ Disclaimer

Tool ini dibuat untuk tujuan edukasi dan penggunaan personal. Pastikan Anda mematuhi terms of service Z-Library dan hukum copyright yang berlaku di wilayah Anda.

## 🆘 Support

Jika mengalami masalah:
1. Check troubleshooting section
2. Verify configuration
3. Check logs untuk error messages
4. Create issue di repository

---

**Happy Reading! 📚**
