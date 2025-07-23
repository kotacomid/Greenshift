# 🚀 AI Theme Generator for WordPress

Sistem otomatis untuk generate template WordPress modern dari konfigurasi JSON menggunakan AI dan Jinja2. Fokus pada bisnis Indonesia dengan design Tailwind CSS yang responsif dan performa tinggi.

## 🎯 Workflow AI → JSON → Python → WordPress

```
AI/GPT → JSON Config → Python + Jinja2 → HTML Templates → WordPress ZIP → Upload & Integration
```

## ⚡ Quick Start

### 1. Setup Environment
```bash
# Clone repository
git clone <repository-url>
cd ai-theme-generator

# Install dependencies (if available)
pip install -r requirements.txt

# Or create virtual environment
python3 -m venv venv
source venv/bin/activate
pip install jinja2 pyyaml
```

### 2. Generate Sample Configurations
```bash
# Generate sample JSON files
python3 generate_samples.py

# Or create samples using main script
python3 template_generator.py --create-samples
```

### 3. Generate Theme dari JSON
```bash
# Generate single theme
python3 template_generator.py --json sample_configs/theme_1_klinik.json

# Generate multiple themes
python3 template_generator.py --multiple sample_configs/*.json

# Custom theme name
python3 template_generator.py --json config.json --theme-name "MyCustomTheme"
```

## 📁 Struktur Project

```
ai-theme-generator/
├── template_generator.py      # Main script untuk generate themes
├── generate_samples.py        # Script untuk create sample JSON
├── config.yaml               # Konfigurasi utama system
├── requirements.txt          # Python dependencies
├── templates/                # Jinja2 templates
│   ├── base_template.html    # Main HTML template
│   ├── functions.php         # WordPress functions
│   ├── main.js              # JavaScript template  
│   ├── header.php           # WordPress header
│   ├── footer.php           # WordPress footer
│   └── index.php            # WordPress index
├── sample_configs/          # Sample JSON configurations
│   ├── theme_1_klinik.json  # Healthcare template
│   ├── theme_2_sedot_wc.json # Sanitation services
│   ├── theme_3_service_hp.json # Tech repair
│   ├── theme_4_sekolah.json # Education platform
│   └── theme_5_bengkel.json # Automotive service
└── generated_themes/        # Output directory
    ├── HealthCare Modern/   # Generated theme folder
    │   ├── style.css
    │   ├── functions.php
    │   ├── index.html
    │   └── assets/
    └── HealthCare Modern.zip # WordPress-ready ZIP
```

## 🎨 Supported Industries & Templates

### 1. Healthcare (Klinik)
- **Features**: Telemedicine, EMR, Appointment booking
- **Colors**: Blue (#3b82f6) + Green (#10b981)
- **Use Case**: Klinik, rumah sakit, praktek dokter

### 2. Sanitation (Sedot WC)
- **Features**: IoT monitoring, Real-time tracking, Emergency service
- **Colors**: Green (#10b981) + Blue (#3b82f6)
- **Use Case**: Layanan sanitasi, sedot WC, maintenance

### 3. Technology (Service HP)
- **Features**: AI diagnostics, Express repair, Device tracking
- **Colors**: Orange (#f59e0b) + Red (#ef4444)
- **Use Case**: Service elektronik, repair smartphone, IT support

### 4. Education (Sekolah)
- **Features**: Virtual classroom, LMS, AI personalization
- **Colors**: Purple (#8b5cf6) + Blue (#3b82f6)
- **Use Case**: Sekolah, universitas, kursus online

### 5. Automotive (Bengkel)
- **Features**: Booking online, Service tracking, Fleet management
- **Colors**: Red (#ef4444) + Orange (#f59e0b)
- **Use Case**: Bengkel mobil, spare part, car wash

## 🔧 JSON Configuration Format

```json
{
  "theme_name": "Nama Theme",
  "description": "Deskripsi theme untuk bisnis Indonesia",
  "industry": "healthcare|sanitation|technology|education|automotive",
  "business_type": "klinik|sedot_wc|service_hp|sekolah|bengkel",
  "colors": {
    "primary": "#3b82f6",
    "secondary": "#10b981", 
    "accent": "#f59e0b"
  },
  "sections": {
    "hero": {
      "title": "Judul Hero Section",
      "subtitle": "Subtitle yang menjelaskan value proposition",
      "cta_primary": "Button utama",
      "cta_secondary": "Button kedua"
    },
    "services": [
      {
        "title": "Nama Layanan",
        "description": "Deskripsi layanan detail",
        "icon": "heroicon-name",
        "features": ["Feature 1", "Feature 2", "Feature 3"]
      }
    ],
    "testimonials": [
      {
        "name": "Nama Customer",
        "role": "Jabatan - Perusahaan",
        "content": "Testimoni lengkap dengan hasil konkret",
        "rating": 5,
        "stats": {
          "metric1": "100%",
          "metric2": "+50%"
        }
      }
    ]
  },
  "contact": {
    "phone": "+62 21 1234 5678",
    "email": "email@domain.com",
    "address": "Alamat lengkap",
    "whatsapp": "+62 812 3456 7890"
  },
  "animation_style": "smooth|bouncy|minimal"
}
```

## 🎯 WordPress Integration

### Auto-Generated Files
- `style.css` - WordPress theme header & Tailwind CSS
- `functions.php` - Theme setup, widgets, custom post types
- `index.php` - Main template file
- `header.php` - Site header with navigation
- `footer.php` - Site footer with widgets
- `single.php` - Single post template
- `page.php` - Static page template
- `archive.php` - Archive pages
- `404.php` - Error page

### Features Yang Dihasilkan
- ✅ Responsive design (mobile-first)
- ✅ Tailwind CSS integration
- ✅ Custom post types (services, testimonials)
- ✅ WordPress Customizer integration
- ✅ Contact form dengan AJAX
- ✅ WhatsApp floating button
- ✅ SEO optimization
- ✅ Performance optimization
- ✅ Security enhancements

### Upload ke WordPress
```bash
# Upload manual
1. Extract generated ZIP file
2. Upload ke wp-content/themes/
3. Activate via WordPress admin

# Auto upload (jika dikonfigurasi)
python3 template_generator.py --json config.json --auto-upload
```

## 🚀 Advanced Features

### Animation Styles
- **Smooth**: Transisi halus dan elegan
- **Bouncy**: Animasi spring dengan bounce effect  
- **Minimal**: Animasi sederhana dan cepat

### Performance Optimization
- Lazy loading images
- Critical CSS inline
- Deferred JavaScript
- Optimized font loading
- Minified assets (optional)

### SEO Features
- Structured data (JSON-LD)
- Open Graph meta tags
- Twitter Card integration
- XML sitemap
- Meta descriptions

### Security Features
- CSRF protection
- Input sanitization
- Output escaping
- Version hiding
- File editing disabled

## 📊 Analytics & Tracking

### Built-in Tracking
- Page load performance
- User interaction events
- WhatsApp click tracking
- Form submission analytics
- Conversion tracking

### Integration Support
- Google Analytics 4
- Facebook Pixel
- Custom event tracking
- Heat mapping tools
- A/B testing ready

## 🎨 Customization Options

### Colors & Branding
```yaml
# Customize via config.yaml
default_colors:
  primary: "#your-primary-color"
  secondary: "#your-secondary-color"
  accent: "#your-accent-color"
```

### Typography
- Google Fonts integration
- Font pairing recommendations
- Responsive typography scale
- Custom font weights

### Layout Options
- Header styles (fixed/sticky/transparent)
- Footer layouts (1-4 columns)
- Sidebar positions
- Container widths

## 🔄 Workflow Integration

### AI → JSON Generation
```python
# Example AI prompt untuk generate JSON:
"""
Generate JSON config untuk klinik gigi modern di Jakarta dengan:
- Telemedicine consultation
- Online appointment booking  
- Digital payment integration
- Patient management system
- WhatsApp integration

Target: Klinik gigi dengan 5-10 dokter
Lokasi: Jakarta Selatan
Budget: Premium service
"""
```

### Automated Pipeline
```bash
# Script untuk pipeline otomatis
#!/bin/bash
echo "🤖 Starting AI Theme Generation..."

# 1. Generate JSON from AI
python3 ai_to_json.py --prompt="$AI_PROMPT"

# 2. Generate WordPress theme
python3 template_generator.py --json ai_generated.json

# 3. Deploy to staging
python3 deploy.py --target=staging

echo "✅ Theme deployed to staging!"
```

## 📱 Mobile-First Design

### Responsive Breakpoints
- Mobile: 320px - 768px
- Tablet: 768px - 1024px  
- Desktop: 1024px+
- Large: 1440px+

### Touch-Friendly Features
- Large touch targets (44px minimum)
- Swipe gestures
- Mobile navigation
- Touch-optimized forms

## 🌐 Internationalization

### Language Support
- Bahasa Indonesia (default)
- English
- Translation ready (.pot files)
- RTL support (optional)

### Localization Features
- Indonesian currency (Rupiah)
- Local phone number format
- Jakarta timezone
- Indonesian address format

## 🧪 Testing & Quality

### Automated Testing
```bash
# Run theme tests
python3 -m pytest tests/

# Performance testing
python3 test_performance.py --theme=generated_theme

# WordPress compatibility
python3 test_wp_compatibility.py
```

### Quality Checks
- W3C HTML validation
- CSS validation
- JavaScript linting
- WordPress coding standards
- Accessibility (WCAG 2.1)

## 📈 Performance Metrics

### Target Performance
- Page Load: < 2 seconds
- First Contentful Paint: < 1.5s
- Largest Contentful Paint: < 2.5s
- Cumulative Layout Shift: < 0.1
- First Input Delay: < 100ms

### Optimization Techniques
- Critical CSS inline
- Resource preloading
- Image optimization
- Code splitting
- CDN integration

## 🛠️ Development & Contribution

### Development Setup
```bash
# Clone & setup
git clone <repo>
cd ai-theme-generator
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt

# Run tests
python3 -m pytest

# Generate documentation
python3 generate_docs.py
```

### Contributing Guidelines
1. Fork repository
2. Create feature branch
3. Add tests for new features
4. Update documentation
5. Submit pull request

## 📚 Resources & Documentation

### Template Examples
- [Healthcare Template Demo](./examples/healthcare/)
- [Sanitation Service Demo](./examples/sanitation/)
- [Tech Repair Demo](./examples/technology/)

### API Documentation
- [JSON Schema Reference](./docs/json-schema.md)
- [Jinja2 Template Guide](./docs/templates.md)
- [WordPress Integration](./docs/wordpress.md)

### Tutorials
- [Creating Custom Industry Templates](./tutorials/custom-industry.md)
- [Advanced Jinja2 Techniques](./tutorials/advanced-templates.md)
- [Performance Optimization](./tutorials/performance.md)

## 🆘 Troubleshooting

### Common Issues

**Error: Module 'jinja2' not found**
```bash
pip install jinja2 pyyaml
```

**Error: Permission denied (WordPress upload)**
```bash
chmod 755 wp-content/themes/
chown www-data:www-data wp-content/themes/
```

**Theme not appearing in WordPress**
- Check style.css header format
- Verify file permissions
- Check for PHP syntax errors

### Debug Mode
```bash
# Enable debug mode
python3 template_generator.py --json config.json --debug

# Verbose output
python3 template_generator.py --json config.json --verbose
```

## 📞 Support & Community

- **Documentation**: [Full documentation](./docs/)
- **Issues**: [GitHub Issues](./issues)
- **Discussions**: [GitHub Discussions](./discussions)
- **WhatsApp**: [Community Group](https://wa.me/group-link)

## 📄 License

MIT License - Open source untuk komunitas developer Indonesia

---

**🎯 Tujuan Akhir**: Generate dari AI → JSON → Python + Jinja → HTML → WordPress → Banyak theme modern untuk bisnis Indonesia!

*Built with ❤️ for Indonesian businesses*
