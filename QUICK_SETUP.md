# 🚀 Quick Setup Guide - GreenShift Business AI Generator

This guide will help you set up the complete AI-powered business website builder in minutes.

## 📋 Prerequisites

✅ **WordPress 6.0+**  
✅ **PHP 7.4+**  
✅ **GreenShift plugin** (recommended but not required)  
✅ **AI API Key** (OpenAI, Claude, or Gemini)  

## 🎯 5-Minute Installation

### Step 1: Upload Plugin
```bash
# Upload to your WordPress plugins directory
wp-content/plugins/greenshift-business-ai-generator/
```

### Step 2: Activate Plugin
1. Go to **WordPress Admin → Plugins**
2. Find "GreenShift Business AI Generator"
3. Click **"Activate"**

### Step 3: Add Sample Data (Optional)
```sql
-- Execute in phpMyAdmin or WP-CLI
-- Replace 'wp_' with your actual table prefix
source sample-data.sql
```

### Step 4: Configure AI API
1. Go to **WordPress Admin → AI Business Gen → Settings**
2. Add your AI API key:

**OpenAI (Recommended):**
```
API Key: sk-...
Model: gpt-3.5-turbo or gpt-4
```

**Claude:**
```
API Key: sk-ant-...
Model: claude-3-sonnet
```

**Gemini:**
```
API Key: AIza...
Model: gemini-pro
```

### Step 5: Generate Your First Page
1. Go to **WordPress Admin → AI Business Gen**
2. Click **"Generate Page"**
3. Follow the 3-step wizard:
   - Choose template
   - Enter business info
   - Add contact details
4. Click **"Generate with AI"**

## 🎨 Sample Businesses Included

The sample data includes 5 realistic business profiles:

| Business | Type | Color Scheme | Industry |
|----------|------|--------------|----------|
| **Tech Solutions Pro** | Technology | Blue Gradient | Software/Consulting |
| **Green Garden Landscaping** | Landscaping | Green Tones | Outdoor Services |
| **Downtown Coffee Roasters** | Food & Beverage | Brown/Orange | Coffee Shop |
| **Elite Fitness Studio** | Fitness | Red Gradient | Health & Wellness |
| **Coastal Real Estate** | Real Estate | Blue Theme | Property Sales |

## 🔧 Configuration Options

### AI Settings
```php
// Recommended settings for best results
Max Tokens: 2000
Temperature: 0.7
Page Status: Draft (review before publishing)
```

### Brand Colors
```css
/* Default color palette */
Primary: #667eea
Secondary: #764ba2
Accent: Based on business type
```

## 📱 Features Available

✨ **For All Users:**
- Generate unlimited business pages
- Use pre-built templates
- Upload logos and set brand colors
- AI content generation
- Page preview and editing

🔧 **For Admins:**
- Template management (create/edit/delete)
- API configuration
- Usage statistics
- User activity monitoring

## 🎯 Quick Test

1. **Dashboard**: Go to `AI Business Gen` in admin menu
2. **Generate**: Click "Generate Page" button
3. **Template**: Select "Modern Business Landing"
4. **Business**: Use "Tech Solutions Pro" sample data
5. **Generate**: Click "Generate with AI"
6. **Result**: Preview your generated page!

## 🔍 Template Structure

Each template includes:
- **Hero Section** with business branding
- **Features/Services** showcase
- **About Preview** with compelling copy
- **Contact Section** with business details
- **SEO Optimization** automatically applied
- **Mobile Responsive** design
- **GreenShift Blocks** integration

## 📊 Usage Analytics

Track your AI generation:
- Pages generated per user
- Most popular templates
- AI model performance
- Business profile usage

## 🛠️ Troubleshooting

### Common Issues:

**"AI Connection Failed"**
```
✅ Check API key is correct
✅ Verify API service status
✅ Test connection in Settings
```

**"Page Generation Slow"**
```
✅ Reduce max_tokens to 1500
✅ Check server timeout limits
✅ Monitor AI service status
```

**"Missing GreenShift Blocks"**
```
✅ Install GreenShift plugin
✅ Verify block registration
✅ Check template structure
```

## 🚀 Next Steps

1. **Customize Templates**: Create your own templates in Template Manager
2. **Brand Guidelines**: Set default colors and styles in Settings
3. **User Training**: Share dashboard access with team members
4. **API Optimization**: Monitor usage and adjust settings
5. **SEO Enhancement**: Review and customize SEO settings

## 📞 Support

- **Documentation**: Check main README.md
- **Sample Data**: Use provided business profiles
- **Templates**: 3 professional templates included
- **Community**: WordPress.org plugin support

---

**🎉 You're ready to create professional business websites with AI in minutes!**

**Next Action**: Go to `AI Business Gen` in your WordPress admin and click "Generate Page" to start!