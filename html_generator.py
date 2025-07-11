import os
from typing import List, Dict, Optional
from jinja2 import Template
from datetime import datetime
from csv_manager import CSVManager
from config import Config

class HTMLGenerator:
    """Generator class for creating HTML pages from book data"""
    
    def __init__(self, csv_manager: CSVManager = None):
        self.csv_manager = csv_manager or CSVManager()
        self.output_path = Config.HTML_OUTPUT_PATH
    
    def get_html_template(self) -> str:
        """
        Get the HTML template for book catalog
        
        Returns:
            str: HTML template string
        """
        template = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Library Book Catalog</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        
        .filters {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .filter-group input, .filter-group select {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .filter-group input:focus, .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .book-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }
        
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .book-header {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .book-cover {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #666;
        }
        
        .book-info {
            flex: 1;
        }
        
        .book-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        .book-authors {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        
        .book-details {
            margin-top: 15px;
        }
        
        .book-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 0.85em;
        }
        
        .meta-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        
        .meta-label {
            font-weight: bold;
            color: #555;
        }
        
        .meta-value {
            color: #777;
        }
        
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffeaa7;
            color: #d63031;
        }
        
        .status-downloading {
            background: #74b9ff;
            color: white;
        }
        
        .status-completed {
            background: #00b894;
            color: white;
        }
        
        .status-error {
            background: #d63031;
            color: white;
        }
        
        .download-links {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .download-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
            margin-right: 10px;
            margin-bottom: 5px;
            transition: background 0.3s ease;
        }
        
        .download-btn:hover {
            background: #5a67d8;
        }
        
        .download-btn.disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .rating {
            color: #f39c12;
            font-weight: bold;
        }
        
        .search-box {
            width: 300px;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #667eea;
            border-radius: 25px;
            margin-bottom: 20px;
        }
        
        .no-books {
            text-align: center;
            color: white;
            font-size: 1.2em;
            margin: 50px 0;
        }
        
        @media (max-width: 768px) {
            .books-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Z-Library Book Catalog</h1>
            <p>Your personal digital library collection</p>
            <p><small>Last updated: {{ last_updated }}</small></p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">{{ stats.total_books }}</div>
                <div>Total Books</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ stats.downloaded_books }}</div>
                <div>Downloaded</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ stats.books_with_drive_links }}</div>
                <div>In Drive</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ stats.unique_languages|length }}</div>
                <div>Languages</div>
            </div>
        </div>
        
        <div class="filters">
            <div class="filter-group">
                <input type="text" id="searchBox" class="search-box" placeholder="🔍 Search books, authors, or titles...">
                
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="downloading">Downloading</option>
                    <option value="completed">Completed</option>
                    <option value="error">Error</option>
                </select>
                
                <select id="languageFilter">
                    <option value="">All Languages</option>
                    {% for lang in stats.unique_languages %}
                    <option value="{{ lang }}">{{ lang|title }}</option>
                    {% endfor %}
                </select>
                
                <select id="extensionFilter">
                    <option value="">All Formats</option>
                    {% for ext in stats.unique_extensions %}
                    <option value="{{ ext }}">{{ ext|upper }}</option>
                    {% endfor %}
                </select>
            </div>
        </div>
        
        <div class="books-grid" id="booksGrid">
            {% for book in books %}
            <div class="book-card" 
                 data-status="{{ book.download_status }}" 
                 data-language="{{ book.language }}" 
                 data-extension="{{ book.extension }}"
                 data-title="{{ book.title|lower }}"
                 data-authors="{{ book.authors|lower }}">
                
                <div class="status-badge status-{{ book.download_status }}">
                    {{ book.download_status }}
                </div>
                
                <div class="book-header">
                    <div class="book-cover">
                        {% if book.cover %}
                        <img src="{{ book.cover }}" alt="{{ book.title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                        {% else %}
                        📖
                        {% endif %}
                    </div>
                    
                    <div class="book-info">
                        <div class="book-title">{{ book.title }}</div>
                        <div class="book-authors">by {{ book.authors }}</div>
                        {% if book.rating %}
                        <div class="rating">⭐ {{ book.rating }}</div>
                        {% endif %}
                    </div>
                </div>
                
                <div class="book-details">
                    <div class="book-meta">
                        <div class="meta-item">
                            <span class="meta-label">Year:</span>
                            <span class="meta-value">{{ book.year or 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Language:</span>
                            <span class="meta-value">{{ book.language|title }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Format:</span>
                            <span class="meta-value">{{ book.extension|upper }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Size:</span>
                            <span class="meta-value">{{ book.size or 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Publisher:</span>
                            <span class="meta-value">{{ book.publisher or 'N/A' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Added:</span>
                            <span class="meta-value">{{ book.added_date[:10] if book.added_date else 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="download-links">
                        {% if book.drive_link %}
                        <a href="{{ book.drive_link }}" class="download-btn" target="_blank">
                            📁 View in Drive
                        </a>
                        {% endif %}
                        
                        {% if book.url %}
                        <a href="{{ book.url }}" class="download-btn" target="_blank">
                            🌐 Z-Library Page
                        </a>
                        {% endif %}
                        
                        {% if not book.drive_link and not book.url %}
                        <span class="download-btn disabled">No links available</span>
                        {% endif %}
                    </div>
                </div>
            </div>
            {% endfor %}
        </div>
        
        {% if not books %}
        <div class="no-books">
            <h2>📭 No books found</h2>
            <p>Start by searching and adding books to your collection!</p>
        </div>
        {% endif %}
    </div>
    
    <script>
        // Search functionality
        document.getElementById('searchBox').addEventListener('input', filterBooks);
        document.getElementById('statusFilter').addEventListener('change', filterBooks);
        document.getElementById('languageFilter').addEventListener('change', filterBooks);
        document.getElementById('extensionFilter').addEventListener('change', filterBooks);
        
        function filterBooks() {
            const searchTerm = document.getElementById('searchBox').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const languageFilter = document.getElementById('languageFilter').value;
            const extensionFilter = document.getElementById('extensionFilter').value;
            
            const bookCards = document.querySelectorAll('.book-card');
            
            bookCards.forEach(card => {
                const title = card.dataset.title;
                const authors = card.dataset.authors;
                const status = card.dataset.status;
                const language = card.dataset.language;
                const extension = card.dataset.extension;
                
                const matchesSearch = !searchTerm || 
                    title.includes(searchTerm) || 
                    authors.includes(searchTerm);
                
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesLanguage = !languageFilter || language === languageFilter;
                const matchesExtension = !extensionFilter || extension === extensionFilter;
                
                if (matchesSearch && matchesStatus && matchesLanguage && matchesExtension) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Auto-refresh every 5 minutes
        setTimeout(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>
        """
        return template
    
    def generate_catalog_html(self, books: List[Dict] = None, output_path: str = None) -> str:
        """
        Generate HTML catalog from book data
        
        Args:
            books: List of book dictionaries (optional, loads from CSV if not provided)
            output_path: Path to save HTML file (optional, uses config if not provided)
            
        Returns:
            str: Path to generated HTML file
        """
        try:
            # Load books if not provided
            if books is None:
                books = self.csv_manager.get_all_books()
            
            # Generate statistics
            stats = self.csv_manager.get_statistics()
            
            # Extract unique values for filters
            unique_languages = list(set([book.get('language', '') for book in books if book.get('language')]))
            unique_extensions = list(set([book.get('extension', '') for book in books if book.get('extension')]))
            
            stats['unique_languages'] = sorted(unique_languages)
            stats['unique_extensions'] = sorted(unique_extensions)
            
            # Prepare template data
            template_data = {
                'books': books,
                'stats': stats,
                'last_updated': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            }
            
            # Generate HTML
            template = Template(self.get_html_template())
            html_content = template.render(**template_data)
            
            # Save HTML file
            output_path = output_path or self.output_path
            with open(output_path, 'w', encoding='utf-8') as f:
                f.write(html_content)
            
            print(f"✅ Generated HTML catalog: {output_path}")
            print(f"📊 Total books: {len(books)}")
            print(f"📈 Statistics: {stats['total_books']} total, {stats['downloaded_books']} downloaded")
            
            return output_path
            
        except Exception as e:
            print(f"❌ Failed to generate HTML catalog: {str(e)}")
            return ""
    
    def generate_book_detail_html(self, book_id: str, output_path: str = None) -> str:
        """
        Generate HTML page for a specific book
        
        Args:
            book_id: ID of the book
            output_path: Path to save HTML file
            
        Returns:
            str: Path to generated HTML file
        """
        try:
            book = self.csv_manager.get_book_by_id(book_id)
            if not book:
                print(f"❌ Book with ID {book_id} not found")
                return ""
            
            output_path = output_path or f"book_{book_id}.html"
            
            # Simple book detail template
            template_html = f"""
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{book.get('title', 'Unknown')} - Book Details</title>
    <style>
        body {{ font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }}
        .book-detail {{ background: #f9f9f9; padding: 20px; border-radius: 10px; }}
        .cover {{ float: left; margin-right: 20px; max-width: 200px; }}
        .info {{ overflow: hidden; }}
        .download-links {{ margin-top: 20px; }}
        .download-btn {{ display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px; }}
    </style>
</head>
<body>
    <div class="book-detail">
        <h1>{book.get('title', 'Unknown Title')}</h1>
        <div class="info">
            <p><strong>Authors:</strong> {book.get('authors', 'Unknown')}</p>
            <p><strong>Year:</strong> {book.get('year', 'N/A')}</p>
            <p><strong>Publisher:</strong> {book.get('publisher', 'N/A')}</p>
            <p><strong>Language:</strong> {book.get('language', 'N/A')}</p>
            <p><strong>Format:</strong> {book.get('extension', 'N/A')}</p>
            <p><strong>Size:</strong> {book.get('size', 'N/A')}</p>
            <p><strong>Rating:</strong> {book.get('rating', 'N/A')}</p>
            <p><strong>Status:</strong> {book.get('download_status', 'Unknown')}</p>
        </div>
        
        <div class="download-links">
            {'<a href="' + book.get('drive_link', '') + '" class="download-btn">View in Google Drive</a>' if book.get('drive_link') else ''}
            {'<a href="' + book.get('url', '') + '" class="download-btn">View on Z-Library</a>' if book.get('url') else ''}
        </div>
    </div>
</body>
</html>
            """
            
            with open(output_path, 'w', encoding='utf-8') as f:
                f.write(template_html)
            
            print(f"✅ Generated book detail HTML: {output_path}")
            return output_path
            
        except Exception as e:
            print(f"❌ Failed to generate book detail HTML: {str(e)}")
            return ""
    
    def generate_stats_html(self, output_path: str = "stats.html") -> str:
        """
        Generate HTML page with collection statistics
        
        Args:
            output_path: Path to save HTML file
            
        Returns:
            str: Path to generated HTML file
        """
        try:
            stats = self.csv_manager.get_statistics()
            
            template_html = f"""
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Statistics</title>
    <style>
        body {{ font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }}
        .stat-grid {{ display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }}
        .stat-card {{ background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; }}
        .stat-number {{ font-size: 2em; font-weight: bold; color: #007bff; }}
        .chart {{ margin: 20px 0; }}
    </style>
</head>
<body>
    <h1>📊 Collection Statistics</h1>
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-number">{stats.get('total_books', 0)}</div>
            <div>Total Books</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{stats.get('downloaded_books', 0)}</div>
            <div>Downloaded Books</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{stats.get('books_with_drive_links', 0)}</div>
            <div>Books in Drive</div>
        </div>
    </div>
    
    <div class="chart">
        <h2>Status Distribution</h2>
        <ul>
            {chr(10).join([f'<li>{status}: {count}</li>' for status, count in stats.get('status_counts', {}).items()])}
        </ul>
    </div>
    
    <div class="chart">
        <h2>Language Distribution</h2>
        <ul>
            {chr(10).join([f'<li>{lang}: {count}</li>' for lang, count in stats.get('language_counts', {}).items()])}
        </ul>
    </div>
    
    <div class="chart">
        <h2>Format Distribution</h2>
        <ul>
            {chr(10).join([f'<li>{ext}: {count}</li>' for ext, count in stats.get('extension_counts', {}).items()])}
        </ul>
    </div>
    
    <p><small>Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}</small></p>
</body>
</html>
            """
            
            with open(output_path, 'w', encoding='utf-8') as f:
                f.write(template_html)
            
            print(f"✅ Generated statistics HTML: {output_path}")
            return output_path
            
        except Exception as e:
            print(f"❌ Failed to generate statistics HTML: {str(e)}")
            return ""

# Example usage
if __name__ == "__main__":
    # Test HTML generator
    html_gen = HTMLGenerator()
    
    # Generate main catalog
    catalog_path = html_gen.generate_catalog_html()
    print(f"Generated catalog: {catalog_path}")
    
    # Generate statistics page
    stats_path = html_gen.generate_stats_html()
    print(f"Generated statistics: {stats_path}")