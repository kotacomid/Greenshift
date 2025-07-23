#!/usr/bin/env python3
"""
Template Generator for WordPress Themes
Generates HTML templates from JSON data using Jinja2
Supports multiple theme variations and automatic WordPress integration
"""

import json
import os
import shutil
import zipfile
from datetime import datetime
from pathlib import Path
from jinja2 import Environment, FileSystemLoader, Template
import argparse
import yaml

class WordPressTemplateGenerator:
    def __init__(self, config_file="config.yaml"):
        self.config = self.load_config(config_file)
        self.jinja_env = Environment(
            loader=FileSystemLoader('templates'),
            autoescape=True,
            trim_blocks=True,
            lstrip_blocks=True
        )
        self.output_dir = Path("generated_themes")
        self.output_dir.mkdir(exist_ok=True)
        
    def load_config(self, config_file):
        """Load configuration from YAML file"""
        try:
            with open(config_file, 'r', encoding='utf-8') as f:
                return yaml.safe_load(f)
        except FileNotFoundError:
            return {
                "default_colors": {
                    "primary": "#3b82f6",
                    "secondary": "#10b981", 
                    "accent": "#f59e0b"
                },
                "wordpress": {
                    "upload_path": "/var/www/html/wp-content/themes/",
                    "auto_upload": False
                }
            }
    
    def generate_from_json(self, json_file, theme_name=None):
        """Generate WordPress theme from JSON configuration"""
        print(f"🚀 Generating theme from {json_file}...")
        
        # Load JSON data
        with open(json_file, 'r', encoding='utf-8') as f:
            data = json.load(f)
        
        # Set theme name
        if not theme_name:
            theme_name = data.get('theme_name', f"ai_theme_{datetime.now().strftime('%Y%m%d_%H%M%S')}")
        
        # Create theme directory
        theme_dir = self.output_dir / theme_name
        theme_dir.mkdir(exist_ok=True)
        
        # Generate all templates
        self.generate_html_template(data, theme_dir)
        self.generate_wordpress_files(data, theme_dir)
        self.generate_css_files(data, theme_dir)
        self.generate_js_files(data, theme_dir)
        self.generate_php_templates(data, theme_dir)
        
        # Create ZIP package
        zip_path = self.create_zip_package(theme_dir, theme_name)
        
        # Auto upload to WordPress if enabled
        if self.config.get('wordpress', {}).get('auto_upload', False):
            self.upload_to_wordpress(zip_path, theme_name)
        
        print(f"✅ Theme '{theme_name}' generated successfully!")
        print(f"📁 Location: {theme_dir}")
        print(f"📦 ZIP Package: {zip_path}")
        
        return theme_dir, zip_path
    
    def generate_html_template(self, data, output_dir):
        """Generate main HTML template using Jinja2"""
        template = self.jinja_env.get_template('base_template.html')
        
        # Process data for template
        template_data = self.process_template_data(data)
        
        # Generate HTML
        html_content = template.render(**template_data)
        
        # Save HTML file
        html_file = output_dir / "index.html"
        with open(html_file, 'w', encoding='utf-8') as f:
            f.write(html_content)
        
        print(f"📄 Generated: {html_file}")
    
    def process_template_data(self, data):
        """Process and enhance JSON data for template rendering"""
        # Merge with default configuration
        processed_data = {
            **self.config.get('defaults', {}),
            **data
        }
        
        # Add generated values
        processed_data.update({
            'generated_at': datetime.now().isoformat(),
            'generator_version': '1.0.0',
            'tailwind_config': self.generate_tailwind_config(processed_data),
            'css_classes': self.generate_css_classes(processed_data),
            'animations': self.generate_animations(processed_data)
        })
        
        return processed_data
    
    def generate_tailwind_config(self, data):
        """Generate Tailwind CSS configuration"""
        colors = data.get('colors', self.config['default_colors'])
        
        return {
            'theme': {
                'extend': {
                    'colors': {
                        'primary': colors.get('primary', '#3b82f6'),
                        'secondary': colors.get('secondary', '#10b981'),
                        'accent': colors.get('accent', '#f59e0b')
                    },
                    'fontFamily': {
                        'sans': data.get('fonts', {}).get('sans', ['Inter', 'sans-serif']),
                        'heading': data.get('fonts', {}).get('heading', ['Poppins', 'sans-serif'])
                    },
                    'animation': {
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'scale-in': 'scaleIn 0.5s ease-out'
                    }
                }
            }
        }
    
    def generate_css_classes(self, data):
        """Generate utility CSS classes based on data"""
        colors = data.get('colors', {})
        
        return {
            'gradient_primary': f"bg-gradient-to-r from-{colors.get('primary', 'blue-600')} to-{colors.get('secondary', 'purple-600')}",
            'btn_primary': f"bg-{colors.get('primary', 'blue-600')} hover:bg-{colors.get('primary', 'blue-700')} text-white px-6 py-3 rounded-lg font-semibold transition-all",
            'card_hover': "bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
        }
    
    def generate_animations(self, data):
        """Generate animation configurations"""
        animation_style = data.get('animation_style', 'smooth')
        
        animations = {
            'smooth': {
                'duration': '0.3s',
                'easing': 'ease-out'
            },
            'bouncy': {
                'duration': '0.5s', 
                'easing': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)'
            },
            'minimal': {
                'duration': '0.2s',
                'easing': 'linear'
            }
        }
        
        return animations.get(animation_style, animations['smooth'])
    
    def generate_wordpress_files(self, data, output_dir):
        """Generate WordPress-specific files"""
        # Generate style.css
        self.generate_style_css(data, output_dir)
        
        # Generate functions.php
        self.generate_functions_php(data, output_dir)
        
        # Generate screenshot.png info
        self.generate_screenshot_info(data, output_dir)
    
    def generate_style_css(self, data, output_dir):
        """Generate WordPress theme style.css header"""
        template = Template("""/*
Theme Name: {{ theme_name }}
Description: {{ description }}
Author: {{ author }}
Version: {{ version }}
License: {{ license }}
License URI: {{ license_uri }}
Text Domain: {{ text_domain }}
Tags: {{ tags | join(', ') }}
Requires at least: {{ wp_version }}
Tested up to: {{ wp_tested }}
Requires PHP: {{ php_version }}
*/

/* Tailwind CSS will be loaded via CDN or compiled */
@import url('https://cdn.tailwindcss.com');

/* Custom styles */
{{ custom_css }}
""")
        
        style_content = template.render(
            theme_name=data.get('theme_name', 'AI Generated Theme'),
            description=data.get('description', 'Modern responsive theme generated by AI'),
            author=data.get('author', 'AI Theme Generator'),
            version=data.get('version', '1.0.0'),
            license=data.get('license', 'GPL v2 or later'),
            license_uri=data.get('license_uri', 'https://www.gnu.org/licenses/gpl-2.0.html'),
            text_domain=data.get('text_domain', 'ai-theme'),
            tags=data.get('tags', ['modern', 'responsive', 'business', 'tailwind']),
            wp_version=data.get('wp_version', '5.0'),
            wp_tested=data.get('wp_tested', '6.4'),
            php_version=data.get('php_version', '7.4'),
            custom_css=data.get('custom_css', '')
        )
        
        with open(output_dir / "style.css", 'w', encoding='utf-8') as f:
            f.write(style_content)
    
    def generate_functions_php(self, data, output_dir):
        """Generate WordPress functions.php"""
        template = self.jinja_env.get_template('functions.php')
        
        functions_content = template.render(
            theme_slug=data.get('text_domain', 'ai-theme'),
            tailwind_config=json.dumps(self.generate_tailwind_config(data), indent=2),
            features=data.get('wordpress_features', [
                'post-thumbnails', 'menus', 'widgets', 'custom-header', 'custom-background'
            ]),
            custom_post_types=data.get('custom_post_types', []),
            contact_form=data.get('contact_form', True)
        )
        
        with open(output_dir / "functions.php", 'w', encoding='utf-8') as f:
            f.write(functions_content)
    
    def generate_php_templates(self, data, output_dir):
        """Generate WordPress PHP template files"""
        templates = [
            'header.php', 'footer.php', 'index.php', 
            'single.php', 'page.php', 'archive.php', '404.php'
        ]
        
        for template_name in templates:
            if template_name in self.jinja_env.list_templates():
                template = self.jinja_env.get_template(template_name)
                content = template.render(**self.process_template_data(data))
                
                with open(output_dir / template_name, 'w', encoding='utf-8') as f:
                    f.write(content)
                
                print(f"📄 Generated: {template_name}")
    
    def generate_css_files(self, data, output_dir):
        """Generate additional CSS files"""
        css_dir = output_dir / "assets" / "css"
        css_dir.mkdir(parents=True, exist_ok=True)
        
        # Generate custom CSS
        if 'custom_styles' in data:
            custom_css = self.jinja_env.get_template('custom.css')
            content = custom_css.render(**self.process_template_data(data))
            
            with open(css_dir / "custom.css", 'w', encoding='utf-8') as f:
                f.write(content)
    
    def generate_js_files(self, data, output_dir):
        """Generate JavaScript files"""
        js_dir = output_dir / "assets" / "js"
        js_dir.mkdir(parents=True, exist_ok=True)
        
        # Generate main JS file
        js_template = self.jinja_env.get_template('main.js')
        content = js_template.render(**self.process_template_data(data))
        
        with open(js_dir / "main.js", 'w', encoding='utf-8') as f:
            f.write(content)
    
    def generate_screenshot_info(self, data, output_dir):
        """Generate screenshot placeholder info"""
        screenshot_info = {
            'required_size': '1200x900px',
            'format': 'PNG or JPG',
            'filename': 'screenshot.png',
            'description': 'Theme preview screenshot'
        }
        
        with open(output_dir / "screenshot-info.txt", 'w', encoding='utf-8') as f:
            f.write("Screenshot Requirements:\n")
            for key, value in screenshot_info.items():
                f.write(f"{key}: {value}\n")
    
    def create_zip_package(self, theme_dir, theme_name):
        """Create ZIP package for WordPress upload"""
        zip_path = self.output_dir / f"{theme_name}.zip"
        
        with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zipf:
            for file_path in theme_dir.rglob('*'):
                if file_path.is_file():
                    arcname = file_path.relative_to(theme_dir.parent)
                    zipf.write(file_path, arcname)
        
        return zip_path
    
    def upload_to_wordpress(self, zip_path, theme_name):
        """Upload theme to WordPress (requires proper setup)"""
        # This would need proper WordPress REST API or file system access
        wp_upload_path = Path(self.config['wordpress']['upload_path'])
        
        if wp_upload_path.exists():
            # Extract to WordPress themes directory
            with zipfile.ZipFile(zip_path, 'r') as zipf:
                zipf.extractall(wp_upload_path)
            
            print(f"📤 Uploaded to WordPress: {wp_upload_path / theme_name}")
        else:
            print(f"⚠️  WordPress path not found: {wp_upload_path}")
    
    def generate_multiple_themes(self, json_files):
        """Generate multiple themes from multiple JSON files"""
        generated_themes = []
        
        for json_file in json_files:
            try:
                theme_dir, zip_path = self.generate_from_json(json_file)
                generated_themes.append({
                    'json_file': json_file,
                    'theme_dir': theme_dir,
                    'zip_path': zip_path,
                    'status': 'success'
                })
            except Exception as e:
                print(f"❌ Error generating theme from {json_file}: {e}")
                generated_themes.append({
                    'json_file': json_file,
                    'status': 'error',
                    'error': str(e)
                })
        
        return generated_themes

def create_sample_json():
    """Create sample JSON configuration files"""
    sample_configs = [
        {
            "theme_name": "HealthCare Modern",
            "description": "Modern healthcare and clinic website template",
            "industry": "healthcare",
            "colors": {
                "primary": "#3b82f6",
                "secondary": "#10b981",
                "accent": "#f59e0b"
            },
            "business_type": "klinik",
            "sections": {
                "hero": {
                    "title": "Revolusi Pelayanan Kesehatan Digital",
                    "subtitle": "Platform telemedicine dan manajemen pasien terdepan",
                    "cta_primary": "Konsultasi Gratis",
                    "cta_secondary": "Lihat Demo"
                },
                "services": [
                    {
                        "title": "Telemedicine",
                        "description": "Konsultasi online dengan dokter spesialis",
                        "icon": "video-camera",
                        "features": ["Video call HD", "Chat real-time", "Resep digital"]
                    },
                    {
                        "title": "EMR System", 
                        "description": "Electronic Medical Records terintegrasi",
                        "icon": "document-text",
                        "features": ["Cloud storage", "Multi-device", "Secure backup"]
                    }
                ],
                "testimonials": [
                    {
                        "name": "Dr. Ahmad Fauzi",
                        "role": "Direktur Klinik Sehat",
                        "content": "Platform ini mengubah cara kami melayani pasien",
                        "rating": 5,
                        "image": "doctor1.jpg"
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 1234 5678",
                "email": "info@kliniksehat.com",
                "address": "Jl. Kesehatan No. 123, Jakarta",
                "whatsapp": "+62 812 3456 7890"
            },
            "wordpress_features": ["post-thumbnails", "menus", "widgets", "custom-post-types"],
            "custom_post_types": ["testimonials", "services", "doctors"],
            "animation_style": "smooth"
        },
        {
            "theme_name": "SanitasiPro Digital",
            "description": "Modern sanitation and waste management services",
            "industry": "sanitation", 
            "colors": {
                "primary": "#10b981",
                "secondary": "#3b82f6",
                "accent": "#f59e0b"
            },
            "business_type": "sedot_wc",
            "sections": {
                "hero": {
                    "title": "Layanan Sedot WC Profesional 24/7",
                    "subtitle": "Booking online dengan tracking real-time",
                    "cta_primary": "Booking Sekarang",
                    "cta_secondary": "Cek Harga"
                },
                "services": [
                    {
                        "title": "Sedot WC Darurat",
                        "description": "Layanan 24 jam untuk kebutuhan mendesak",
                        "icon": "clock",
                        "features": ["Respon cepat", "Tim profesional", "Harga transparan"]
                    },
                    {
                        "title": "Maintenance Rutin",
                        "description": "Perawatan berkala untuk pencegahan",
                        "icon": "calendar",
                        "features": ["Jadwal fleksibel", "Reminder otomatis", "Diskon member"]
                    }
                ]
            },
            "contact": {
                "phone": "+62 21 9876 5432", 
                "email": "info@sanitasipro.com",
                "whatsapp": "+62 811 9876 5432"
            },
            "animation_style": "bouncy"
        },
        {
            "theme_name": "TechRepair Center",
            "description": "Modern electronics and smartphone repair services",
            "industry": "technology",
            "colors": {
                "primary": "#f59e0b",
                "secondary": "#ef4444", 
                "accent": "#8b5cf6"
            },
            "business_type": "service_hp",
            "sections": {
                "hero": {
                    "title": "Service HP & Elektronik Terpercaya",
                    "subtitle": "AI diagnostics dengan garansi resmi",
                    "cta_primary": "Diagnosis Gratis",
                    "cta_secondary": "Cek Garansi"
                },
                "services": [
                    {
                        "title": "Repair Smartphone",
                        "description": "Perbaikan semua merk dengan spare part original",
                        "icon": "device-mobile",
                        "features": ["Garansi resmi", "Spare part original", "Teknisi certified"]
                    }
                ]
            },
            "animation_style": "minimal"
        }
    ]
    
    # Create sample JSON files
    samples_dir = Path("sample_configs")
    samples_dir.mkdir(exist_ok=True)
    
    for i, config in enumerate(sample_configs):
        filename = samples_dir / f"theme_{i+1}_{config['business_type']}.json"
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(config, f, indent=2, ensure_ascii=False)
        
        print(f"📄 Created sample: {filename}")

def main():
    parser = argparse.ArgumentParser(description="Generate WordPress themes from JSON using AI")
    parser.add_argument('--json', type=str, help='JSON configuration file')
    parser.add_argument('--multiple', nargs='+', help='Multiple JSON files')
    parser.add_argument('--create-samples', action='store_true', help='Create sample JSON files')
    parser.add_argument('--theme-name', type=str, help='Custom theme name')
    parser.add_argument('--config', type=str, default='config.yaml', help='Configuration file')
    
    args = parser.parse_args()
    
    if args.create_samples:
        create_sample_json()
        return
    
    generator = WordPressTemplateGenerator(args.config)
    
    if args.multiple:
        print("🔄 Generating multiple themes...")
        results = generator.generate_multiple_themes(args.multiple)
        
        print("\n📊 Generation Summary:")
        for result in results:
            status_icon = "✅" if result['status'] == 'success' else "❌"
            print(f"{status_icon} {result['json_file']} - {result['status']}")
            
    elif args.json:
        generator.generate_from_json(args.json, args.theme_name)
    else:
        print("❌ Please provide --json file or --create-samples")
        parser.print_help()

if __name__ == "__main__":
    main()