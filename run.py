#!/usr/bin/env python3
"""
Z-Library Automation Tool - Quick Runner
Shortcut untuk menjalankan aplikasi dalam berbagai mode
"""

import asyncio
import sys
import argparse
from datetime import datetime

def print_banner():
    """Print application banner"""
    print("=" * 60)
    print("📚 Z-Library Automation Tool")
    print("   Automated book management workflow")
    print("=" * 60)
    print(f"   Started at: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 60)
    print()

def print_menu():
    """Print main menu"""
    print("🎯 Available Modes:")
    print("1. 🔍 Interactive Mode - Full interactive interface")
    print("2. 🌐 Web Dashboard - Start web interface")
    print("3. 🚀 Full Workflow - Complete automation (search → download → upload → HTML)")
    print("4. ⬇️ Download Only - Download pending books")
    print("5. ☁️ Upload Only - Upload to Google Drive")
    print("6. 🌐 Generate HTML - Create book catalog")
    print("7. 📊 Show Statistics - Display collection stats")
    print("8. ⚙️ Setup - Run initial setup")
    print("9. 🧪 Test Connection - Test Z-Library and Drive connections")
    print("0. ❌ Exit")
    print()

async def test_connections():
    """Test Z-Library and Google Drive connections"""
    print("🧪 Testing connections...\n")
    
    try:
        # Test Z-Library
        print("📚 Testing Z-Library connection...")
        from zlibrary_manager import ZLibraryManager
        zlib_manager = ZLibraryManager()
        
        if await zlib_manager.login():
            print("✅ Z-Library: Connected successfully")
        else:
            print("❌ Z-Library: Authentication failed")
        
        await zlib_manager.close()
        
    except Exception as e:
        print(f"❌ Z-Library: Error - {e}")
    
    try:
        # Test Google Drive
        print("\n📁 Testing Google Drive connection...")
        from drive_manager import DriveManager
        drive_manager = DriveManager()
        
        if drive_manager.authenticate():
            print("✅ Google Drive: Connected successfully")
            
            # Test folder access
            files = drive_manager.list_files(max_results=1)
            print(f"   Can access {len(files)} file(s)")
        else:
            print("❌ Google Drive: Authentication failed")
            
    except Exception as e:
        print(f"❌ Google Drive: Error - {e}")
    
    print("\n✅ Connection test completed")

def show_stats():
    """Show collection statistics"""
    try:
        from csv_manager import CSVManager
        csv_manager = CSVManager()
        stats = csv_manager.get_statistics()
        
        print("📊 Collection Statistics:")
        print(f"   📚 Total Books: {stats.get('total_books', 0)}")
        print(f"   ✅ Downloaded: {stats.get('downloaded_books', 0)}")
        print(f"   ☁️ In Google Drive: {stats.get('books_with_drive_links', 0)}")
        print(f"   🌍 Languages: {len(stats.get('language_counts', {}))}")
        print(f"   📄 Formats: {len(stats.get('extension_counts', {}))}")
        
        print("\n📈 Status Breakdown:")
        for status, count in stats.get('status_counts', {}).items():
            print(f"   {status}: {count}")
            
        print("\n🌍 Top Languages:")
        for lang, count in list(stats.get('language_counts', {}).items())[:5]:
            print(f"   {lang}: {count}")
        
    except Exception as e:
        print(f"❌ Error getting statistics: {e}")

async def run_workflow():
    """Run complete workflow with user input"""
    query = input("📖 Enter search query: ").strip()
    if not query:
        print("❌ Query cannot be empty")
        return
    
    try:
        count = int(input("📊 Number of books (default 10): ") or "10")
    except ValueError:
        count = 10
    
    print(f"\n🚀 Starting workflow for '{query}' ({count} books)...")
    
    try:
        from main import ZLibraryAutomation
        automation = ZLibraryAutomation()
        
        if await automation.initialize():
            results = await automation.run_full_workflow(query, count)
            
            print("\n📋 Results:")
            print(f"   🔍 Books found: {results['books_found']}")
            print(f"   ⬇️ Books downloaded: {results['books_downloaded']}")
            print(f"   ☁️ Files uploaded: {results['files_uploaded']}")
            print(f"   🌐 HTML generated: {results['html_generated']}")
            
            if results['html_path']:
                print(f"   📄 HTML file: {results['html_path']}")
        
        await automation.cleanup()
        
    except Exception as e:
        print(f"❌ Workflow error: {e}")

def start_web():
    """Start web interface"""
    print("🌐 Starting web dashboard...")
    print("   Access at: http://localhost:5000")
    print("   Press Ctrl+C to stop")
    print()
    
    try:
        from app import app
        from config import Config
        app.run(
            host=Config.FLASK_HOST,
            port=Config.FLASK_PORT,
            debug=Config.FLASK_DEBUG
        )
    except KeyboardInterrupt:
        print("\n👋 Web dashboard stopped")
    except Exception as e:
        print(f"❌ Error starting web dashboard: {e}")

async def download_only():
    """Download pending books only"""
    print("⬇️ Downloading pending books...")
    
    try:
        from main import ZLibraryAutomation
        automation = ZLibraryAutomation()
        
        if await automation.initialize():
            count = await automation.download_selected_books()
            print(f"✅ Downloaded {count} books")
        
        await automation.cleanup()
        
    except Exception as e:
        print(f"❌ Download error: {e}")

async def upload_only():
    """Upload completed books to Drive only"""
    print("☁️ Uploading completed books to Google Drive...")
    
    try:
        from main import ZLibraryAutomation
        automation = ZLibraryAutomation()
        
        if await automation.initialize():
            count = await automation.upload_to_drive_and_update_csv()
            print(f"✅ Uploaded {count} files")
        
        await automation.cleanup()
        
    except Exception as e:
        print(f"❌ Upload error: {e}")

def generate_html():
    """Generate HTML catalog only"""
    print("🌐 Generating HTML catalog...")
    
    try:
        from html_generator import HTMLGenerator
        html_gen = HTMLGenerator()
        
        html_path = html_gen.generate_catalog_html()
        if html_path:
            print(f"✅ HTML catalog generated: {html_path}")
        else:
            print("❌ Failed to generate HTML catalog")
    
    except Exception as e:
        print(f"❌ HTML generation error: {e}")

async def interactive_mode():
    """Start interactive mode"""
    print("🎯 Starting interactive mode...\n")
    
    try:
        from main import ZLibraryAutomation
        automation = ZLibraryAutomation()
        
        if await automation.initialize():
            await automation.interactive_mode()
        
        await automation.cleanup()
        
    except Exception as e:
        print(f"❌ Interactive mode error: {e}")

def run_setup():
    """Run setup script"""
    print("⚙️ Running setup...\n")
    
    try:
        import setup
        setup.main()
    except Exception as e:
        print(f"❌ Setup error: {e}")

async def main():
    """Main function"""
    # Check for command line arguments
    if len(sys.argv) > 1:
        arg = sys.argv[1].lower()
        
        if arg in ['web', 'dashboard', 'server']:
            start_web()
        elif arg in ['interactive', 'i']:
            await interactive_mode()
        elif arg in ['test', 'check']:
            await test_connections()
        elif arg in ['stats', 'statistics']:
            show_stats()
        elif arg in ['setup', 'install']:
            run_setup()
        elif arg in ['download', 'dl']:
            await download_only()
        elif arg in ['upload', 'up']:
            await upload_only()
        elif arg in ['html', 'generate']:
            generate_html()
        elif arg in ['help', 'h', '--help']:
            print("Usage: python run.py [mode]")
            print("Modes: web, interactive, test, stats, setup, download, upload, html")
        else:
            print(f"❌ Unknown mode: {arg}")
            print("Use 'python run.py help' for available modes")
        
        return
    
    # Interactive menu mode
    print_banner()
    
    while True:
        print_menu()
        
        try:
            choice = input("👆 Select option (0-9): ").strip()
            
            if choice == '0':
                print("👋 Goodbye!")
                break
            elif choice == '1':
                await interactive_mode()
            elif choice == '2':
                start_web()
            elif choice == '3':
                await run_workflow()
            elif choice == '4':
                await download_only()
            elif choice == '5':
                await upload_only()
            elif choice == '6':
                generate_html()
            elif choice == '7':
                show_stats()
            elif choice == '8':
                run_setup()
            elif choice == '9':
                await test_connections()
            else:
                print("❌ Invalid choice. Please try again.")
                
            print("\n" + "─" * 60 + "\n")
            
        except KeyboardInterrupt:
            print("\n\n👋 Goodbye!")
            break
        except Exception as e:
            print(f"❌ Error: {e}")
            print("\n" + "─" * 60 + "\n")

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\n\n👋 Goodbye!")
    except Exception as e:
        print(f"❌ Fatal error: {e}")
        sys.exit(1)