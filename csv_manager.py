import pandas as pd
import os
from typing import List, Dict, Optional
from datetime import datetime
from config import Config

class CSVManager:
    """Manager class for CSV operations to store book metadata"""
    
    def __init__(self, csv_path: str = None):
        self.csv_path = csv_path or Config.CSV_FILE_PATH
        self.columns = [
            'id', 'title', 'authors', 'year', 'publisher', 'language', 
            'extension', 'size', 'rating', 'url', 'cover', 'isbn',
            'search_query', 'download_status', 'download_url', 'local_path', 
            'drive_link', 'added_date', 'updated_date'
        ]
        self.initialize_csv()
    
    def initialize_csv(self):
        """Initialize CSV file if it doesn't exist"""
        if not os.path.exists(self.csv_path):
            df = pd.DataFrame(columns=self.columns)
            df.to_csv(self.csv_path, index=False)
            print(f"✅ Created new CSV file: {self.csv_path}")
        else:
            print(f"✅ Using existing CSV file: {self.csv_path}")
    
    def load_data(self) -> pd.DataFrame:
        """
        Load data from CSV file
        
        Returns:
            pd.DataFrame: DataFrame containing book data
        """
        try:
            if os.path.exists(self.csv_path):
                df = pd.read_csv(self.csv_path)
                print(f"✅ Loaded {len(df)} records from CSV")
                return df
            else:
                return pd.DataFrame(columns=self.columns)
        except Exception as e:
            print(f"❌ Failed to load CSV data: {str(e)}")
            return pd.DataFrame(columns=self.columns)
    
    def save_data(self, df: pd.DataFrame):
        """
        Save DataFrame to CSV file
        
        Args:
            df: DataFrame to save
        """
        try:
            df.to_csv(self.csv_path, index=False)
            print(f"✅ Saved {len(df)} records to CSV")
        except Exception as e:
            print(f"❌ Failed to save CSV data: {str(e)}")
    
    def add_books(self, books: List[Dict]) -> int:
        """
        Add new books to CSV file
        
        Args:
            books: List of book dictionaries
            
        Returns:
            int: Number of books added
        """
        try:
            df = self.load_data()
            existing_ids = set(df['id'].astype(str)) if not df.empty else set()
            
            new_books = []
            current_time = datetime.now().isoformat()
            
            for book in books:
                book_id = str(book.get('id', ''))
                if book_id not in existing_ids:
                    # Prepare book data
                    book_data = {
                        'id': book_id,
                        'title': book.get('title', ''),
                        'authors': ', '.join(book.get('authors', [])) if isinstance(book.get('authors'), list) else str(book.get('authors', '')),
                        'year': book.get('year', ''),
                        'publisher': book.get('publisher', ''),
                        'language': book.get('language', ''),
                        'extension': book.get('extension', ''),
                        'size': book.get('size', ''),
                        'rating': book.get('rating', ''),
                        'url': book.get('url', ''),
                        'cover': book.get('cover', ''),
                        'isbn': book.get('isbn', ''),
                        'search_query': book.get('search_query', ''),
                        'download_status': book.get('download_status', 'pending'),
                        'download_url': book.get('download_url', ''),
                        'local_path': book.get('local_path', ''),
                        'drive_link': book.get('drive_link', ''),
                        'added_date': current_time,
                        'updated_date': current_time
                    }
                    new_books.append(book_data)
                    existing_ids.add(book_id)
            
            if new_books:
                new_df = pd.DataFrame(new_books)
                df = pd.concat([df, new_df], ignore_index=True)
                self.save_data(df)
                print(f"✅ Added {len(new_books)} new books to CSV")
                return len(new_books)
            else:
                print("ℹ️ No new books to add (all books already exist)")
                return 0
                
        except Exception as e:
            print(f"❌ Failed to add books to CSV: {str(e)}")
            return 0
    
    def update_book_status(self, book_id: str, status: str, **kwargs) -> bool:
        """
        Update book download status and other fields
        
        Args:
            book_id: ID of the book to update
            status: New download status
            **kwargs: Additional fields to update
            
        Returns:
            bool: True if update successful, False otherwise
        """
        try:
            df = self.load_data()
            if df.empty:
                print("❌ No data in CSV to update")
                return False
            
            # Find the book
            mask = df['id'].astype(str) == str(book_id)
            if not mask.any():
                print(f"❌ Book with ID {book_id} not found in CSV")
                return False
            
            # Update fields
            df.loc[mask, 'download_status'] = status
            df.loc[mask, 'updated_date'] = datetime.now().isoformat()
            
            for key, value in kwargs.items():
                if key in self.columns:
                    df.loc[mask, key] = value
            
            self.save_data(df)
            print(f"✅ Updated book {book_id} with status: {status}")
            return True
            
        except Exception as e:
            print(f"❌ Failed to update book status: {str(e)}")
            return False
    
    def get_books_by_status(self, status: str) -> List[Dict]:
        """
        Get books with specific download status
        
        Args:
            status: Download status to filter by
            
        Returns:
            List[Dict]: List of books with the specified status
        """
        try:
            df = self.load_data()
            if df.empty:
                return []
            
            filtered_df = df[df['download_status'] == status]
            books = filtered_df.to_dict('records')
            print(f"✅ Found {len(books)} books with status: {status}")
            return books
            
        except Exception as e:
            print(f"❌ Failed to get books by status: {str(e)}")
            return []
    
    def get_book_by_id(self, book_id: str) -> Optional[Dict]:
        """
        Get a specific book by ID
        
        Args:
            book_id: Book ID to search for
            
        Returns:
            Dict: Book data or None if not found
        """
        try:
            df = self.load_data()
            if df.empty:
                return None
            
            mask = df['id'].astype(str) == str(book_id)
            if not mask.any():
                return None
            
            book = df[mask].iloc[0].to_dict()
            return book
            
        except Exception as e:
            print(f"❌ Failed to get book by ID: {str(e)}")
            return None
    
    def get_all_books(self) -> List[Dict]:
        """
        Get all books from CSV
        
        Returns:
            List[Dict]: List of all books
        """
        try:
            df = self.load_data()
            if df.empty:
                return []
            
            books = df.to_dict('records')
            print(f"✅ Retrieved {len(books)} books from CSV")
            return books
            
        except Exception as e:
            print(f"❌ Failed to get all books: {str(e)}")
            return []
    
    def search_books(self, query: str, column: str = 'title') -> List[Dict]:
        """
        Search books in CSV by column
        
        Args:
            query: Search query
            column: Column to search in
            
        Returns:
            List[Dict]: List of matching books
        """
        try:
            df = self.load_data()
            if df.empty:
                return []
            
            if column not in df.columns:
                print(f"❌ Column {column} not found in CSV")
                return []
            
            # Case-insensitive search
            mask = df[column].astype(str).str.contains(query, case=False, na=False)
            filtered_df = df[mask]
            books = filtered_df.to_dict('records')
            print(f"✅ Found {len(books)} books matching '{query}' in {column}")
            return books
            
        except Exception as e:
            print(f"❌ Failed to search books: {str(e)}")
            return []
    
    def get_statistics(self) -> Dict:
        """
        Get statistics about the book collection
        
        Returns:
            Dict: Statistics about the collection
        """
        try:
            df = self.load_data()
            if df.empty:
                return {'total_books': 0}
            
            stats = {
                'total_books': len(df),
                'status_counts': df['download_status'].value_counts().to_dict(),
                'language_counts': df['language'].value_counts().to_dict(),
                'extension_counts': df['extension'].value_counts().to_dict(),
                'books_with_drive_links': len(df[df['drive_link'].notna() & (df['drive_link'] != '')]),
                'downloaded_books': len(df[df['download_status'] == 'completed'])
            }
            
            print(f"✅ Generated statistics for {stats['total_books']} books")
            return stats
            
        except Exception as e:
            print(f"❌ Failed to get statistics: {str(e)}")
            return {'total_books': 0}

# Example usage
if __name__ == "__main__":
    # Test CSV manager
    csv_manager = CSVManager("test_books.csv")
    
    # Test adding books
    sample_books = [
        {
            'id': '123',
            'title': 'Test Book 1',
            'authors': ['Author 1', 'Author 2'],
            'year': '2023',
            'publisher': 'Test Publisher',
            'language': 'english',
            'extension': 'PDF',
            'size': '10 MB',
            'rating': '4.5/5.0',
            'search_query': 'test'
        },
        {
            'id': '456',
            'title': 'Test Book 2',
            'authors': ['Author 3'],
            'year': '2022',
            'publisher': 'Another Publisher',
            'language': 'english',
            'extension': 'EPUB',
            'size': '5 MB',
            'rating': '3.8/5.0',
            'search_query': 'test'
        }
    ]
    
    # Add books
    added_count = csv_manager.add_books(sample_books)
    print(f"Added {added_count} books")
    
    # Get statistics
    stats = csv_manager.get_statistics()
    print(f"Statistics: {stats}")
    
    # Update book status
    csv_manager.update_book_status('123', 'downloaded', local_path='/path/to/file.pdf')
    
    # Search books
    books = csv_manager.search_books('Test Book')
    print(f"Found {len(books)} books with 'Test Book' in title")