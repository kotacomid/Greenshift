#!/usr/bin/env python3
"""
Setup script untuk Z-Library Automation Tool
Membantu instalasi dan konfigurasi awal
"""

import os
import sys
import subprocess
from pathlib import Path

def print_header():
    print("=" * 60)
    print("🚀 Z-Library Automation Tool - Setup")
    print("=" * 60)
    print()

def check_python_version():
    """Check if Python version is compatible"""
    if sys.version_info < (3, 7):
        print("❌ Python 3.7+ diperlukan!")
        print(f"   Versi Anda: {sys.version}")
        return False
    print(f"✅ Python {sys.version.split()[0]} - OK")
    return True

def install_dependencies():
    """Install required dependencies"""
    print("\n📦 Installing dependencies...")
    try:
        subprocess.check_call([sys.executable, '-m', 'pip', 'install', '-r', 'requirements.txt'])
        print("✅ Dependencies installed successfully")
        return True
    except subprocess.CalledProcessError:
        print("❌ Failed to install dependencies")
        print("   Try: pip install -r requirements.txt")
        return False

def setup_env_file():
    """Setup .env file from template"""
    print("\n⚙️ Setting up configuration...")
    
    if os.path.exists('.env'):
        print("✅ .env file already exists")
        return True
    
    if not os.path.exists('.env.example'):
        print("❌ .env.example file not found")
        return False
    
    # Copy template
    with open('.env.example', 'r') as src, open('.env', 'w') as dst:
        dst.write(src.read())
    
    print("✅ Created .env file from template")
    print("⚠️  Please edit .env file with your credentials:")
    print("   - ZLIBRARY_EMAIL")
    print("   - ZLIBRARY_PASSWORD")
    print("   - GOOGLE_CREDENTIALS_FILE (optional)")
    print("   - DRIVE_FOLDER_ID (optional)")
    return True

def create_directories():
    """Create necessary directories"""
    print("\n📁 Creating directories...")
    
    directories = ['downloads', 'logs']
    for directory in directories:
        Path(directory).mkdir(exist_ok=True)
        print(f"✅ Created {directory}/ directory")

def test_import():
    """Test if all modules can be imported"""
    print("\n🧪 Testing imports...")
    
    modules = [
        'config',
        'zlibrary_manager', 
        'csv_manager',
        'drive_manager',
        'html_generator'
    ]
    
    failed = []
    for module in modules:
        try:
            __import__(module)
            print(f"✅ {module}")
        except Exception as e:
            print(f"❌ {module}: {e}")
            failed.append(module)
    
    return len(failed) == 0

def show_next_steps():
    """Show next steps to user"""
    print("\n🎯 Next Steps:")
    print("1. Edit .env file with your credentials:")
    print("   nano .env")
    print()
    print("2. For Google Drive integration (optional):")
    print("   - Download credentials.json from Google Cloud Console")
    print("   - Place it in the root directory")
    print()
    print("3. Test the application:")
    print("   python main.py --interactive")
    print()
    print("4. Start web interface:")
    print("   python app.py")
    print()
    print("5. For cPanel deployment:")
    print("   - Upload all files to public_html/")
    print("   - Make sure app.py has execute permissions")
    print("   - Configure .htaccess as needed")
    print()
    print("📖 Read README.md for detailed instructions")

def main():
    """Main setup function"""
    print_header()
    
    # Check Python version
    if not check_python_version():
        return 1
    
    # Install dependencies
    if not install_dependencies():
        return 1
    
    # Setup configuration
    if not setup_env_file():
        return 1
    
    # Create directories
    create_directories()
    
    # Test imports
    if not test_import():
        print("\n⚠️  Some modules failed to import")
        print("   This might be due to missing dependencies")
        print("   Try: pip install -r requirements.txt")
    
    print("\n🎉 Setup completed successfully!")
    show_next_steps()
    
    return 0

if __name__ == "__main__":
    exit_code = main()
    sys.exit(exit_code)