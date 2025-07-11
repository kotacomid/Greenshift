# 🚀 GreenShift Business AI Generator - Complete MVP

**AI-Powered Business Website Builder with GreenShift Integration**

Generate professional business pages instantly using AI. Input your business details, select a template, and watch as AI creates beautiful, fully-optimized pages with GreenShift blocks.

## ✨ Features

### 🎯 Core Features
- **AI-Powered Content Generation** - Support for OpenAI, Claude, and Gemini
- **Template-Based System** - Pre-built templates for Landing, About, and Pricing pages
- **User-Friendly Dashboard** - Intuitive interface for all users
- **Real-Time Page Generation** - Creates WordPress pages with GreenShift blocks
- **Automatic SEO Optimization** - Built-in SEO with meta tags and schema markup
- **Logo & Branding Integration** - Upload logos and set brand colors
- **Social Media Integration** - Add social media links automatically

### 🔧 Admin Features
- **Template Management** - Create, edit, and delete custom templates
- **API Configuration** - Easy setup for multiple AI providers
- **Usage Statistics** - Track generated pages and user activity
- **Multi-AI Support** - Switch between different AI models

### 🎨 Templates Included
1. **Modern Business Landing** - Hero section, features, about preview, contact
2. **Professional About Us** - Company story, mission, vision, values, team
3. **Clear Pricing Page** - Service packages, pricing tiers, FAQ section

## 📦 Installation

### Prerequisites
- WordPress 6.0+
- PHP 7.4+
- GreenShift plugin (recommended)
- At least one AI API key (OpenAI, Claude, or Gemini)

### Installation Steps

1. **Upload Plugin Files**
   ```bash
   # Upload to WordPress plugins directory
   wp-content/plugins/greenshift-business-ai-generator/
   ```

2. **Activate Plugin**
   - Go to WordPress Admin → Plugins
   - Find "GreenShift Business AI Generator"
   - Click "Activate"

3. **Configure API Keys**
   - Go to WordPress Admin → AI Business Gen → Settings
   - Add your preferred AI API key:
     - **OpenAI**: Get from [OpenAI Platform](https://platform.openai.com/api-keys)
     - **Claude**: Get from [Anthropic Console](https://console.anthropic.com/)
     - **Gemini**: Get from [Google AI Studio](https://makersuite.google.com/app/apikey)

4. **Start Generating Pages**
   - Go to WordPress Admin → AI Business Gen
   - Click "Generate Page"
   - Follow the 3-step wizard

## 🗄️ Database Schema

The plugin creates 3 custom tables to manage business data, templates, and generated pages:

### Business Profiles Table
```sql
CREATE TABLE wp_gsba_business_profiles (
    id int(11) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    business_name varchar(255) NOT NULL,
    business_type varchar(100) NOT NULL,
    description text NOT NULL,
    logo_url varchar(500) DEFAULT '',
    primary_color varchar(7) DEFAULT '#667eea',
    secondary_color varchar(7) DEFAULT '#764ba2',
    website_url varchar(500) DEFAULT '',
    phone varchar(50) DEFAULT '',
    email varchar(100) DEFAULT '',
    address text DEFAULT '',
    tagline varchar(255) DEFAULT '',
    social_media longtext DEFAULT '',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id)
);
```

### Templates Table
```sql
CREATE TABLE wp_gsba_templates (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    type varchar(50) NOT NULL,
    description text DEFAULT '',
    preview_image varchar(500) DEFAULT '',
    block_structure longtext NOT NULL,
    seo_config longtext DEFAULT '',
    created_by bigint(20) NOT NULL,
    status varchar(20) DEFAULT 'active',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY type (type),
    KEY status (status)
);
```

### Generated Pages Table
```sql
CREATE TABLE wp_gsba_generated_pages (
    id int(11) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    business_profile_id int(11) NOT NULL,
    template_id int(11) NOT NULL,
    page_id bigint(20) NOT NULL,
    generated_content longtext NOT NULL,
    ai_model_used varchar(50) DEFAULT '',
    seo_data longtext DEFAULT '',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY page_id (page_id),
    KEY template_id (template_id)
);
```

## 🎮 Usage Guide

### For Users (Generate Pages)

1. **Access Dashboard**
   - Go to WordPress Admin → AI Business Gen
   - Or click "🤖 AI Generator" in admin bar

2. **Generate New Page**
   - Click "Generate Page" button
   - Follow 3-step wizard:

   **Step 1: Choose Template**
   - Select from available templates
   - Preview template designs

   **Step 2: Business Information**
   - Enter business name (required)
   - Select business type (required)
   - Add business description (required)
   - Set tagline (optional)

   **Step 3: Contact & Branding**
   - Add contact information
   - Upload logo
   - Set brand colors
   - Add social media links

3. **AI Generation**
   - Click "Generate Page with AI"
   - Wait for AI to create content
   - View/edit generated page

### For Admins (Manage Templates)

1. **Template Management**
   - Go to AI Business Gen → Templates
   - View existing templates
   - Add new templates
   - Edit existing templates

2. **Settings Configuration**
   - Go to AI Business Gen → Settings
   - Configure AI API keys
   - Set generation parameters
   - View usage statistics

## 🏗️ Architecture

### File Structure
```
greenshift-business-ai-generator/
├── greenshift-business-ai-generator.php    # Main plugin file
├── includes/
│   ├── class-database.php                  # Database operations
│   ├── class-ai-generator.php              # AI content generation
│   ├── class-template-manager.php          # Template management
│   ├── class-page-generator.php            # WordPress page creation
│   ├── class-seo-optimizer.php             # SEO optimization
│   └── pages/
│       ├── dashboard.php                   # Main dashboard
│       ├── templates.php                   # Template management
│       └── settings.php                    # Plugin settings
├── assets/
│   ├── css/
│   │   ├── admin.css                       # Admin interface styles
│   │   └── frontend.css                    # Frontend styles
│   └── js/
│       ├── admin.js                        # Admin functionality
│       └── frontend.js                     # Frontend interactions
└── README.md                               # Documentation
```

### Data Flow
```
User Input → AI Generator → Content Processing → GreenShift Blocks → WordPress Page → SEO Optimization
```

## 🎨 Template Customization

### Creating Custom Templates

Templates use GreenShift block structure with placeholder variables:

```json
{
    "blockName": "greenshift-blocks/container",
    "attrs": {
        "background": "linear-gradient(135deg, {primary_color}, {secondary_color})",
        "padding": "100px 20px"
    },
    "innerBlocks": [
        {
            "blockName": "core/heading",
            "attrs": {
                "content": "{hero_headline}",
                "level": 1
            }
        }
    ]
}
```

### Available Placeholders

**Business Data:**
- `{business_name}` - Business name
- `{business_type}` - Type of business
- `{description}` - Business description
- `{logo_url}` - Logo image URL
- `{primary_color}` - Primary brand color
- `{secondary_color}` - Secondary brand color
- `{phone}` - Phone number
- `{email}` - Email address
- `{website_url}` - Website URL

**AI Generated Content:**
- `{hero_headline}` - AI-generated headline
- `{hero_subheading}` - AI-generated subheading
- `{feature_1_title}` - First feature title
- `{feature_1_description}` - First feature description
- `{about_heading}` - About section heading
- `{contact_heading}` - Contact section heading

## 🔧 API Configuration

### OpenAI Setup
```php
// Required: OpenAI API Key
$settings['openai_api_key'] = 'sk-...';
$settings['default_ai_model'] = 'openai';
```

### Claude Setup
```php
// Required: Claude API Key
$settings['claude_api_key'] = 'sk-ant-...';
$settings['default_ai_model'] = 'claude';
```

### Gemini Setup
```php
// Required: Gemini API Key
$settings['gemini_api_key'] = 'AIza...';
$settings['default_ai_model'] = 'gemini';
```

## 📊 Sample Data

### Sample Business Profile
```json
{
    "business_name": "Tech Solutions Pro",
    "business_type": "technology",
    "description": "We provide cutting-edge technology solutions for businesses. Our team specializes in custom software development, cloud migration, and digital transformation consulting.",
    "logo_url": "https://example.com/logo.png",
    "primary_color": "#667eea",
    "secondary_color": "#764ba2",
    "phone": "+1 (555) 123-4567",
    "email": "contact@techsolutionspro.com",
    "address": "123 Tech Street, San Francisco, CA 94105",
    "tagline": "Transforming businesses through technology",
    "social_media": {
        "facebook": "https://facebook.com/techsolutionspro",
        "linkedin": "https://linkedin.com/company/techsolutionspro",
        "whatsapp": "+15551234567"
    }
}
```

### Sample AI Generated Content
```json
{
    "hero_headline": "Transform Your Business with Technology",
    "hero_subheading": "We help businesses leverage cutting-edge technology to increase efficiency, reduce costs, and drive growth through custom solutions.",
    "hero_cta_text": "Get Started Today",
    "feature_1": {
        "title": "Custom Development",
        "description": "Tailored software solutions built specifically for your business needs. Our expert developers create scalable applications that grow with your company."
    },
    "feature_2": {
        "title": "Cloud Migration",
        "description": "Seamlessly transition to the cloud with our proven migration strategies. Reduce infrastructure costs while improving performance and security."
    },
    "feature_3": {
        "title": "24/7 Support",
        "description": "Round-the-clock technical support ensures your systems run smoothly. Our dedicated team is always ready to help when you need us most."
    }
}
```

## 🔒 Security Features

- **Nonce Verification** - All AJAX requests are protected
- **Capability Checks** - Role-based access control
- **Input Sanitization** - All user inputs are sanitized
- **SQL Injection Prevention** - Prepared statements used
- **XSS Protection** - Output escaping implemented

## 🚀 Performance

- **Efficient Database Queries** - Optimized with proper indexing
- **AJAX Loading** - Non-blocking page generation
- **Caching Support** - Compatible with WordPress caching
- **Minimal Resource Usage** - Lightweight implementation

## 🐛 Troubleshooting

### Common Issues

**Issue: API Connection Failed**
- Check API key is correct
- Verify API service is active
- Test connection in Settings

**Issue: Page Generation Slow**
- Reduce max_tokens setting
- Check server timeout limits
- Monitor AI service status

**Issue: Missing GreenShift Blocks**
- Install GreenShift plugin
- Check block registration
- Verify template structure

### Debug Mode
```php
// Add to wp-config.php
define('GSBA_DEBUG', true);
```

## 📝 Changelog

### Version 1.0.0
- Initial MVP release
- Complete AI integration (OpenAI, Claude, Gemini)
- 3 default templates (Landing, About, Pricing)
- User dashboard and admin management
- SEO optimization features
- Logo and branding integration
- Social media support

## 🤝 Contributing

1. Fork the repository
2. Create feature branch
3. Make changes
4. Add tests
5. Submit pull request

## 📄 License

GPL v2 or later

## 🆘 Support

- **Documentation**: Check this README
- **Issues**: Create GitHub issue
- **Email**: support@wpsoul.com

---

**🎉 Ready to transform business websites with AI? Install the GreenShift Business AI Generator and start creating professional pages in minutes!**
