import asyncio
import zlibrary
import logging
import os
import aiohttp
from typing import List, Dict, Optional
from config import Config

class ZLibraryManager:
    """Manager class for Z-Library operations"""
    
    def __init__(self):
        self.lib = None
        self.is_authenticated = False
        self.setup_logging()
    
    def setup_logging(self):
        """Setup logging for Z-Library operations"""
        logging.getLogger("zlibrary").addHandler(logging.StreamHandler())
        logging.getLogger("zlibrary").setLevel(logging.INFO)
    
    async def login(self, email: str = None, password: str = None) -> bool:
        """
        Login to Z-Library
        
        Args:
            email: Z-Library email (optional, uses config if not provided)
            password: Z-Library password (optional, uses config if not provided)
            
        Returns:
            bool: True if login successful, False otherwise
        """
        try:
            email = email or Config.ZLIBRARY_EMAIL
            password = password or Config.ZLIBRARY_PASSWORD
            
            if not email or not password:
                raise ValueError("Email and password are required")
            
            self.lib = zlibrary.AsyncZlib()
            await self.lib.login(email, password)
            self.is_authenticated = True
            print(f"✅ Successfully logged in to Z-Library with email: {email}")
            return True
            
        except Exception as e:
            print(f"❌ Failed to login to Z-Library: {str(e)}")
            self.is_authenticated = False
            return False
    
    async def search_books(self, query: str, count: int = 10, **kwargs) -> List[Dict]:
        """
        Search for books in Z-Library
        
        Args:
            query: Search query
            count: Number of results per page
            **kwargs: Additional search parameters (year, language, extension, etc.)
            
        Returns:
            List[Dict]: List of book metadata
        """
        if not self.is_authenticated:
            raise Exception("Not authenticated. Please login first.")
        
        try:
            print(f"🔍 Searching for '{query}' with {count} results...")
            
            # Create paginator with search parameters
            paginator = await self.lib.search(q=query, count=count, **kwargs)
            
            # Get first result set
            results = await paginator.next()
            
            books = []
            for book in results:
                book_data = {
                    'id': book.get('id', ''),
                    'title': book.get('name', ''),
                    'authors': [author.get('author', '') for author in book.get('authors', [])],
                    'year': book.get('year', ''),
                    'publisher': book.get('publisher', ''),
                    'language': book.get('language', ''),
                    'extension': book.get('extension', ''),
                    'size': book.get('size', ''),
                    'rating': book.get('rating', ''),
                    'url': book.get('url', ''),
                    'cover': book.get('cover', ''),
                    'isbn': book.get('isbn', ''),
                    'download_status': 'pending',
                    'download_url': '',
                    'local_path': '',
                    'drive_link': '',
                    'search_query': query
                }
                books.append(book_data)
            
            print(f"✅ Found {len(books)} books for query '{query}'")
            return books
            
        except Exception as e:
            print(f"❌ Failed to search books: {str(e)}")
            return []
    
    async def get_book_details(self, book_id: str) -> Optional[Dict]:
        """
        Get detailed information about a specific book
        
        Args:
            book_id: Book ID from search results
            
        Returns:
            Dict: Detailed book information including download URL
        """
        if not self.is_authenticated:
            raise Exception("Not authenticated. Please login first.")
        
        try:
            # First search to find the book
            paginator = await self.lib.search(q=book_id, count=50)
            results = await paginator.next()
            
            # Find the book with matching ID
            target_book = None
            for book in results:
                if book.get('id') == book_id:
                    target_book = book
                    break
            
            if not target_book:
                print(f"❌ Book with ID {book_id} not found")
                return None
            
            # Fetch detailed information
            detailed_book = await target_book.fetch()
            print(f"✅ Retrieved details for book: {detailed_book.get('name', 'Unknown')}")
            
            return detailed_book
            
        except Exception as e:
            print(f"❌ Failed to get book details: {str(e)}")
            return None
    
    async def download_book(self, book_data: Dict, download_path: str = None) -> str:
        """
        Download a book file
        
        Args:
            book_data: Book metadata dictionary
            download_path: Path to save the downloaded file
            
        Returns:
            str: Path to downloaded file or empty string if failed
        """
        if not self.is_authenticated:
            raise Exception("Not authenticated. Please login first.")
        
        try:
            download_path = download_path or Config.DOWNLOAD_PATH
            os.makedirs(download_path, exist_ok=True)
            
            # Get detailed book information with download URL
            detailed_book = await self.get_book_details(book_data['id'])
            if not detailed_book:
                return ""
            
            download_url = detailed_book.get('download_url', '')
            if not download_url:
                print(f"❌ No download URL available for book: {book_data['title']}")
                return ""
            
            # Generate filename
            title = book_data['title'].replace('/', '_').replace('\\', '_')
            extension = book_data.get('extension', 'pdf').lower()
            filename = f"{title}.{extension}"
            filepath = os.path.join(download_path, filename)
            
            print(f"⬇️ Downloading: {book_data['title']}...")
            
            # Download the file
            async with aiohttp.ClientSession() as session:
                async with session.get(download_url) as response:
                    if response.status == 200:
                        with open(filepath, 'wb') as f:
                            async for chunk in response.content.iter_chunked(8192):
                                f.write(chunk)
                        
                        print(f"✅ Downloaded: {filename}")
                        return filepath
                    else:
                        print(f"❌ Failed to download {filename}. Status: {response.status}")
                        return ""
            
        except Exception as e:
            print(f"❌ Failed to download book: {str(e)}")
            return ""
    
    async def close(self):
        """Close the Z-Library connection"""
        if self.lib:
            # Close any open connections if the library provides a close method
            self.is_authenticated = False
            print("✅ Z-Library connection closed")

# Example usage function
async def example_usage():
    """Example usage of ZLibraryManager"""
    manager = ZLibraryManager()
    
    try:
        # Login
        if await manager.login():
            # Search for books
            books = await manager.search_books("Python programming", count=5)
            
            for book in books:
                print(f"Found: {book['title']} by {', '.join(book['authors'])}")
                
                # Download first book as example
                if books:
                    downloaded_path = await manager.download_book(books[0])
                    if downloaded_path:
                        print(f"Downloaded to: {downloaded_path}")
                break
        
    finally:
        await manager.close()

if __name__ == "__main__":
    asyncio.run(example_usage())