import os
from dotenv import load_dotenv

load_dotenv()

class Config:
    """Configuration class for Z-Library automation application"""
    
    # Z-Library credentials
    ZLIBRARY_EMAIL = os.getenv('ZLIBRARY_EMAIL', '')
    ZLIBRARY_PASSWORD = os.getenv('ZLIBRARY_PASSWORD', '')
    
    # Google Drive API credentials
    GOOGLE_CREDENTIALS_FILE = os.getenv('GOOGLE_CREDENTIALS_FILE', 'credentials.json')
    GOOGLE_TOKEN_FILE = os.getenv('GOOGLE_TOKEN_FILE', 'token.json')
    
    # Google Drive folder ID untuk upload
    DRIVE_FOLDER_ID = os.getenv('DRIVE_FOLDER_ID', '')
    
    # CSV file settings
    CSV_FILE_PATH = os.getenv('CSV_FILE_PATH', 'zlibrary_books.csv')
    
    # HTML output settings
    HTML_OUTPUT_PATH = os.getenv('HTML_OUTPUT_PATH', 'book_catalog.html')
    
    # Flask settings
    FLASK_SECRET_KEY = os.getenv('FLASK_SECRET_KEY', 'your-secret-key-here')
    FLASK_DEBUG = os.getenv('FLASK_DEBUG', 'False').lower() == 'true'
    FLASK_HOST = os.getenv('FLASK_HOST', '0.0.0.0')
    FLASK_PORT = int(os.getenv('FLASK_PORT', 5000))
    
    # Download settings
    DOWNLOAD_PATH = os.getenv('DOWNLOAD_PATH', 'downloads/')
    
    @classmethod
    def validate_config(cls):
        """Validate required configuration"""
        required_fields = [
            'ZLIBRARY_EMAIL',
            'ZLIBRARY_PASSWORD'
        ]
        
        missing_fields = []
        for field in required_fields:
            if not getattr(cls, field):
                missing_fields.append(field)
        
        if missing_fields:
            raise ValueError(f"Missing required configuration: {', '.join(missing_fields)}")
        
        return True