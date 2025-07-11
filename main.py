#!/usr/bin/env python3
"""
Z-Library Automation Tool
Main application for automating Z-Library workflows:
1. Login and search
2. Store metadata in CSV - choose file
3. Open CSV, download file and update in CSV
4. Update CSV - upload drive - link drive - update CSV
5. Update CSV - HTML - drive link
"""

import asyncio
import os
import sys
from typing import List, Dict, Optional
from datetime import datetime
import argparse

from config import Config
from zlibrary_manager import ZLibraryManager
from csv_manager import CSVManager
from drive_manager import DriveManager
from html_generator import HTMLGenerator

class ZLibraryAutomation:
    """Main automation class for Z-Library workflow"""
    
    def __init__(self):
        self.zlib_manager = ZLibraryManager()
        self.csv_manager = CSVManager()
        self.drive_manager = DriveManager()
        self.html_generator = HTMLGenerator(self.csv_manager)
        self.authenticated = {
            'zlibrary': False,
            'drive': False
        }
    
    async def initialize(self) -> bool:
        """
        Initialize all services
        
        Returns:
            bool: True if initialization successful
        """
        print("🚀 Initializing Z-Library Automation Tool...")
        
        # Validate configuration
        try:
            Config.validate_config()
        except ValueError as e:
            print(f"❌ Configuration error: {e}")
            return False
        
        # Authenticate with Z-Library
        print("\n📚 Authenticating with Z-Library...")
        if await self.zlib_manager.login():
            self.authenticated['zlibrary'] = True
        else:
            print("❌ Failed to authenticate with Z-Library")
            return False
        
        # Authenticate with Google Drive (optional)
        print("\n📁 Authenticating with Google Drive...")
        if self.drive_manager.authenticate():
            self.authenticated['drive'] = True
        else:
            print("⚠️ Google Drive authentication failed - drive upload will be disabled")
        
        print("\n✅ Initialization complete!")
        return True
    
    async def search_and_store(self, query: str, count: int = 10) -> int:
        """
        Step 1 & 2: Login and search, then store metadata in CSV
        
        Args:
            query: Search query
            count: Number of books to search for
            
        Returns:
            int: Number of books added to CSV
        """
        print(f"\n🔍 Step 1-2: Searching for '{query}' and storing metadata...")
        
        if not self.authenticated['zlibrary']:
            print("❌ Not authenticated with Z-Library")
            return 0
        
        # Search for books
        books = await self.zlib_manager.search_books(query, count)
        if not books:
            print("❌ No books found")
            return 0
        
        # Store in CSV
        added_count = self.csv_manager.add_books(books)
        print(f"✅ Added {added_count} new books to CSV")
        
        return added_count
    
    async def download_selected_books(self, book_ids: List[str] = None, status_filter: str = "pending") -> int:
        """
        Step 3: Download selected books and update CSV
        
        Args:
            book_ids: List of specific book IDs to download (optional)
            status_filter: Download books with this status (default: pending)
            
        Returns:
            int: Number of books downloaded
        """
        print(f"\n⬇️ Step 3: Downloading books...")
        
        if not self.authenticated['zlibrary']:
            print("❌ Not authenticated with Z-Library")
            return 0
        
        # Get books to download
        if book_ids:
            books_to_download = []
            for book_id in book_ids:
                book = self.csv_manager.get_book_by_id(book_id)
                if book:
                    books_to_download.append(book)
        else:
            books_to_download = self.csv_manager.get_books_by_status(status_filter)
        
        if not books_to_download:
            print(f"❌ No books found with status '{status_filter}'")
            return 0
        
        print(f"📋 Found {len(books_to_download)} books to download")
        
        downloaded_count = 0
        for book in books_to_download:
            try:
                # Update status to downloading
                self.csv_manager.update_book_status(book['id'], 'downloading')
                
                # Download the book and cover
                download_result = await self.zlib_manager.download_book(book)
                
                if download_result['success']:
                    # Update CSV with success
                    update_data = {
                        'local_path': download_result['book_path']
                    }
                    
                    # Add cover path if downloaded
                    if download_result['cover_path']:
                        update_data['cover_local_path'] = download_result['cover_path']
                    
                    self.csv_manager.update_book_status(
                        book['id'], 
                        'completed',
                        **update_data
                    )
                    downloaded_count += 1
                    print(f"✅ Downloaded: {book['title']}")
                    
                    if download_result['cover_path']:
                        print(f"📸 Cover downloaded: {os.path.basename(download_result['cover_path'])}")
                else:
                    # Update CSV with error
                    self.csv_manager.update_book_status(book['id'], 'error')
                    error_msg = download_result.get('error', 'Unknown error')
                    print(f"❌ Failed to download: {book['title']} - {error_msg}")
                
            except Exception as e:
                print(f"❌ Error downloading {book['title']}: {str(e)}")
                self.csv_manager.update_book_status(book['id'], 'error')
        
        print(f"✅ Downloaded {downloaded_count} books successfully")
        return downloaded_count
    
    async def upload_to_drive_and_update_csv(self, status_filter: str = "completed") -> int:
        """
        Step 4: Upload downloaded files to Google Drive and update CSV with links
        
        Args:
            status_filter: Upload books with this status (default: completed)
            
        Returns:
            int: Number of files uploaded to Drive
        """
        print(f"\n☁️ Step 4: Uploading files to Google Drive...")
        
        if not self.authenticated['drive']:
            print("❌ Not authenticated with Google Drive")
            return 0
        
        # Get books with local files
        completed_books = self.csv_manager.get_books_by_status(status_filter)
        books_to_upload = [book for book in completed_books 
                          if book.get('local_path') and os.path.exists(book['local_path'])
                          and not book.get('drive_link')]
        
        if not books_to_upload:
            print("❌ No downloaded books found to upload")
            return 0
        
        print(f"📋 Found {len(books_to_upload)} books to upload")
        
        uploaded_count = 0
        for book in books_to_upload:
            try:
                from utils import generate_book_filename, generate_cover_filename
                
                book_path = book['local_path']
                cover_path = book.get('cover_local_path', '')
                
                # Generate filenames for Drive
                book_filename = generate_book_filename(book)
                cover_filename = generate_cover_filename(book) if cover_path else None
                
                # Upload book and cover to Google Drive
                upload_result = self.drive_manager.upload_book_and_cover(
                    book_path=book_path,
                    cover_path=cover_path if cover_path and os.path.exists(cover_path) else None,
                    book_name=book_filename,
                    cover_name=cover_filename
                )
                
                if upload_result['success']:
                    # Update CSV with Drive links
                    update_data = {
                        'drive_link': upload_result['book_link']
                    }
                    
                    if upload_result['cover_link']:
                        update_data['cover_drive_link'] = upload_result['cover_link']
                    
                    self.csv_manager.update_book_status(
                        book['id'],
                        'completed',
                        **update_data
                    )
                    uploaded_count += 1
                    print(f"✅ Uploaded to Drive: {book['title']}")
                    print(f"🔗 Book link: {upload_result['book_link']}")
                    
                    if upload_result['cover_link']:
                        print(f"📸 Cover link: {upload_result['cover_link']}")
                else:
                    error_msg = upload_result.get('error', 'Unknown error')
                    print(f"❌ Failed to upload: {book['title']} - {error_msg}")
                
            except Exception as e:
                print(f"❌ Error uploading {book['title']}: {str(e)}")
        
        print(f"✅ Uploaded {uploaded_count} files to Google Drive")
        return uploaded_count
    
    def generate_html_catalog(self) -> str:
        """
        Step 5: Generate HTML catalog with Drive links
        
        Returns:
            str: Path to generated HTML file
        """
        print(f"\n🌐 Step 5: Generating HTML catalog...")
        
        try:
            html_path = self.html_generator.generate_catalog_html()
            if html_path:
                print(f"✅ HTML catalog generated: {html_path}")
                
                # Also generate statistics page
                stats_path = self.html_generator.generate_stats_html()
                if stats_path:
                    print(f"📊 Statistics page generated: {stats_path}")
                
                return html_path
            else:
                print("❌ Failed to generate HTML catalog")
                return ""
                
        except Exception as e:
            print(f"❌ Error generating HTML: {str(e)}")
            return ""
    
    async def run_full_workflow(self, query: str, count: int = 10) -> Dict:
        """
        Run the complete workflow
        
        Args:
            query: Search query
            count: Number of books to search for
            
        Returns:
            Dict: Workflow results summary
        """
        print("🚀 Starting complete Z-Library automation workflow...")
        
        results = {
            'books_found': 0,
            'books_downloaded': 0,
            'files_uploaded': 0,
            'html_generated': False,
            'html_path': ''
        }
        
        try:
            # Step 1-2: Search and store
            results['books_found'] = await self.search_and_store(query, count)
            
            # Step 3: Download books
            if results['books_found'] > 0:
                results['books_downloaded'] = await self.download_selected_books()
            
            # Step 4: Upload to Drive
            if results['books_downloaded'] > 0 and self.authenticated['drive']:
                results['files_uploaded'] = await self.upload_to_drive_and_update_csv()
            
            # Step 5: Generate HTML
            html_path = self.generate_html_catalog()
            if html_path:
                results['html_generated'] = True
                results['html_path'] = html_path
            
            # Summary
            print("\n📋 Workflow Summary:")
            print(f"  🔍 Books found: {results['books_found']}")
            print(f"  ⬇️ Books downloaded: {results['books_downloaded']}")
            print(f"  ☁️ Files uploaded to Drive: {results['files_uploaded']}")
            print(f"  🌐 HTML catalog generated: {results['html_generated']}")
            if results['html_path']:
                print(f"  📄 HTML file: {results['html_path']}")
            
            return results
            
        except Exception as e:
            print(f"❌ Error in workflow: {str(e)}")
            return results
    
    async def interactive_mode(self):
        """Run in interactive mode"""
        print("\n🎯 Z-Library Automation - Interactive Mode")
        print("=" * 50)
        
        while True:
            print("\nAvailable commands:")
            print("1. Search and store books")
            print("2. Download selected books") 
            print("3. Upload to Google Drive")
            print("4. Generate HTML catalog")
            print("5. Run full workflow")
            print("6. Show statistics")
            print("7. List books")
            print("8. Exit")
            
            choice = input("\nEnter your choice (1-8): ").strip()
            
            try:
                if choice == '1':
                    query = input("Enter search query: ").strip()
                    count = int(input("Enter number of books (default 10): ") or "10")
                    await self.search_and_store(query, count)
                
                elif choice == '2':
                    print("\nDownload options:")
                    print("1. Download all pending books")
                    print("2. Download specific book by ID")
                    sub_choice = input("Choose option (1-2): ").strip()
                    
                    if sub_choice == '1':
                        await self.download_selected_books()
                    elif sub_choice == '2':
                        book_id = input("Enter book ID: ").strip()
                        await self.download_selected_books([book_id])
                
                elif choice == '3':
                    await self.upload_to_drive_and_update_csv()
                
                elif choice == '4':
                    self.generate_html_catalog()
                
                elif choice == '5':
                    query = input("Enter search query: ").strip()
                    count = int(input("Enter number of books (default 10): ") or "10")
                    await self.run_full_workflow(query, count)
                
                elif choice == '6':
                    stats = self.csv_manager.get_statistics()
                    print("\n📊 Collection Statistics:")
                    for key, value in stats.items():
                        print(f"  {key}: {value}")
                
                elif choice == '7':
                    books = self.csv_manager.get_all_books()
                    print(f"\n📚 Books in collection ({len(books)}):")
                    for i, book in enumerate(books[:10], 1):  # Show first 10
                        print(f"  {i}. {book['title']} - {book['download_status']}")
                    if len(books) > 10:
                        print(f"  ... and {len(books) - 10} more books")
                
                elif choice == '8':
                    print("👋 Goodbye!")
                    break
                
                else:
                    print("❌ Invalid choice. Please try again.")
                    
            except KeyboardInterrupt:
                print("\n\n👋 Goodbye!")
                break
            except Exception as e:
                print(f"❌ Error: {str(e)}")
    
    async def cleanup(self):
        """Cleanup resources"""
        print("\n🧹 Cleaning up resources...")
        await self.zlib_manager.close()
        print("✅ Cleanup complete")

async def main():
    """Main function"""
    parser = argparse.ArgumentParser(description="Z-Library Automation Tool")
    parser.add_argument("--query", type=str, help="Search query")
    parser.add_argument("--count", type=int, default=10, help="Number of books to search")
    parser.add_argument("--interactive", action="store_true", help="Run in interactive mode")
    parser.add_argument("--download-only", action="store_true", help="Only download pending books")
    parser.add_argument("--upload-only", action="store_true", help="Only upload completed books")
    parser.add_argument("--html-only", action="store_true", help="Only generate HTML catalog")
    
    args = parser.parse_args()
    
    # Create automation instance
    automation = ZLibraryAutomation()
    
    try:
        # Initialize
        if not await automation.initialize():
            print("❌ Failed to initialize. Exiting.")
            return 1
        
        # Run based on arguments
        if args.interactive:
            await automation.interactive_mode()
        elif args.download_only:
            await automation.download_selected_books()
        elif args.upload_only:
            await automation.upload_to_drive_and_update_csv()
        elif args.html_only:
            automation.generate_html_catalog()
        elif args.query:
            await automation.run_full_workflow(args.query, args.count)
        else:
            print("No action specified. Use --help for options or --interactive for interactive mode.")
            return 1
        
        return 0
        
    except KeyboardInterrupt:
        print("\n\n👋 Interrupted by user")
        return 1
    except Exception as e:
        print(f"❌ Unexpected error: {str(e)}")
        return 1
    finally:
        await automation.cleanup()

if __name__ == "__main__":
    # Run the main function
    exit_code = asyncio.run(main())