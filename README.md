# GreenShift Business AI Generator

![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress Compatibility](https://img.shields.io/badge/wordpress-6.0+-green.svg)
![PHP Compatibility](https://img.shields.io/badge/php-7.4+-blue.svg)
![License](https://img.shields.io/badge/license-GPL2+-red.svg)

Plugin WordPress yang memungkinkan pengguna menghasilkan konten bisnis profesional menggunakan AI hanya dengan memasukkan nama bisnis, jenis bisnis, dan deskripsi singkat.

## 🚀 Fitur Utama

- **Integrasi Multi-AI**: Dukungan OpenAI GPT-3.5, Claude 3 Sonnet, dan Google Gemini Pro
- **Generator Konten Bisnis**: 10+ jenis konten bisnis (About Us, Mission/Vision, Services, FAQ, dll.)
- **Integrasi GreenShift**: Kompatibel dengan plugin GreenShift untuk styling lanjutan
- **Blok Gutenberg**: Tersedia sebagai blok Gutenberg yang mudah digunakan
- **Shortcode Support**: Gunakan `[gsba_business_generator]` di mana saja
- **Responsif & Modern**: Interface yang indah dan responsif
- **Copy to Clipboard**: Salin hasil AI dengan satu klik
- **Form Validation**: Validasi form yang komprehensif

## 📋 Prasyarat

- WordPress 6.0 atau lebih baru
- PHP 7.4 atau lebih baru
- Setidaknya satu API key dari provider AI (OpenAI, Claude, atau Gemini)

## 🔧 Instalasi

### Metode 1: Upload Manual

1. Download file plugin ini
2. Upload folder plugin ke direktori `/wp-content/plugins/`
3. Aktifkan plugin melalui menu 'Plugins' di WordPress admin
4. Buka **Settings > Business AI Generator** untuk konfigurasi

### Metode 2: Install dari Admin

1. Buka WordPress admin > Plugins > Add New
2. Upload file ZIP plugin
3. Install dan aktifkan plugin
4. Konfigurasi API keys di halaman settings

## ⚙️ Konfigurasi

### 1. Mendapatkan API Keys

#### OpenAI API Key
1. Buka [OpenAI Platform](https://platform.openai.com/api-keys)
2. Buat akun atau login
3. Generate API key baru
4. Copy API key

#### Claude API Key
1. Buka [Anthropic Console](https://console.anthropic.com/)
2. Buat akun atau login
3. Generate API key
4. Copy API key

#### Gemini API Key
1. Buka [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Buat akun atau login dengan Google
3. Generate API key
4. Copy API key

### 2. Konfigurasi Plugin

1. Buka **WordPress Admin > Settings > Business AI Generator**
2. Masukkan setidaknya satu API key
3. Pilih default AI model
4. Atur max tokens (100-4000)
5. Atur temperature (0-1)
6. Klik **Save Settings**

## 📖 Cara Penggunaan

### Menggunakan Shortcode

Tambahkan shortcode berikut di post, page, atau widget:

```
[gsba_business_generator]
```

#### Shortcode dengan Parameter

```
[gsba_business_generator title="AI Generator Bisnis" show_examples="true" style="modern"]
```

**Parameter yang tersedia:**
- `title`: Judul generator (default: "Business AI Generator")
- `show_examples`: Tampilkan contoh (true/false, default: true)
- `style`: Gaya tampilan (default/compact/modern, default: default)

### Menggunakan Blok Gutenberg

1. Buka editor Gutenberg
2. Klik tombol **+** untuk menambah blok
3. Cari "Business AI Generator"
4. Tambahkan blok ke halaman
5. Konfigurasi pengaturan di sidebar

### Menggunakan dengan GreenShift

Jika Anda menggunakan plugin GreenShift, blok AI Generator akan otomatis tersedia di panel blok GreenShift dengan styling dan opsi tambahan.

## 🎯 Jenis Konten yang Didukung

1. **About Us Section** - Bagian tentang perusahaan
2. **Mission & Vision** - Pernyataan misi dan visi
3. **Services Description** - Deskripsi layanan
4. **Hero Section** - Konten hero/banner utama
5. **Features & Benefits** - Fitur dan keunggulan
6. **Customer Testimonials** - Testimoni pelanggan
7. **FAQ Section** - Pertanyaan yang sering diajukan
8. **Contact Information** - Informasi kontak
9. **Blog Posts** - Artikel blog
10. **Product Descriptions** - Deskripsi produk

## 🏢 Jenis Bisnis yang Didukung

- Restaurant (Restoran)
- Retail Store (Toko Retail)
- Consulting (Konsultan)
- Technology (Teknologi)
- Healthcare (Kesehatan)
- Real Estate (Properti)
- Law Firm (Firma Hukum)
- Fitness & Wellness
- Education (Pendidikan)
- Automotive (Otomotif)
- Construction (Konstruksi)
- Beauty & Salon
- Travel & Tourism (Perjalanan & Pariwisata)
- Financial Services (Layanan Keuangan)
- Marketing Agency (Agensi Pemasaran)
- Dan lainnya...

## 💡 Contoh Penggunaan

### Contoh Input:
- **Nama Bisnis**: "Warung Makan Sejahtera"
- **Jenis Bisnis**: "Restaurant"
- **Deskripsi**: "Warung makan keluarga yang menyajikan masakan Indonesia autentik dengan bumbu tradisional dan suasana yang hangat di pusat kota."
- **Jenis Konten**: "About Us Section"

### Contoh Output:
AI akan menghasilkan konten About Us yang profesional dan menarik berdasarkan informasi yang diberikan.

## 🔒 Keamanan & Privasi

- Plugin menggunakan nonce untuk keamanan AJAX
- API keys disimpan dengan aman di database WordPress
- Data bisnis tidak disimpan secara permanen
- Semua komunikasi dengan API menggunakan HTTPS

## 💰 Biaya API

| Provider | Model | Perkiraan Biaya per 1K Token |
|----------|-------|-------------------------------|
| OpenAI | GPT-3.5 Turbo | ~$0.002 |
| Claude | Claude 3 Sonnet | ~$0.003 |
| Gemini | Gemini Pro | Gratis (dengan batas) |

*Catatan: Biaya dapat berubah. Cek dokumentasi resmi masing-masing provider.*

## 🛠️ Troubleshooting

### Plugin tidak muncul setelah aktivasi
- Pastikan WordPress versi 6.0+
- Pastikan PHP versi 7.4+
- Cek log error WordPress

### API tidak bekerja
- Pastikan API key sudah benar
- Cek koneksi internet
- Pastikan tidak ada firewall yang memblokir

### Form tidak submit
- Pastikan JavaScript aktif di browser
- Cek console browser untuk error
- Pastikan jQuery dimuat dengan benar

### Konten tidak generate
- Pastikan semua field required sudah diisi
- Cek API key dan quota
- Coba dengan AI model yang berbeda

## 🔄 Update Plugin

1. Backup website Anda
2. Download versi terbaru plugin
3. Deaktivasi plugin lama
4. Upload plugin baru
5. Aktifkan kembali plugin
6. Cek pengaturan API keys

## 📞 Support & Kontribusi

### Mendapatkan Support
- Buat issue di repository GitHub
- Kirim email ke developer
- Check dokumentasi WordPress

### Kontribusi
1. Fork repository
2. Buat branch untuk fitur baru
3. Commit perubahan Anda
4. Submit pull request

## 📄 Lisensi

Plugin ini dilisensikan under GPL v2 atau yang lebih baru. Lihat file `LICENSE` untuk detail lengkap.

## 🙏 Kredit

- Dikembangkan berdasarkan framework GreenShift
- Menggunakan API dari OpenAI, Anthropic, dan Google
- Terinspirasi dari plugin Smart Code AI

## 📊 Changelog

### v1.0.0 (2024-01-15)
- Release awal
- Dukungan OpenAI, Claude, dan Gemini
- Blok Gutenberg
- Shortcode support
- 10 jenis konten bisnis
- Interface responsif

---

**Developed with ❤️ for Indonesian WordPress Community**

Jika plugin ini membantu bisnis Anda, jangan lupa berikan ⭐ di repository ini!
