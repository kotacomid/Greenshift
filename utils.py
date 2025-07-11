#!/usr/bin/env python3
"""
Utilities for file operations and naming
Windows and cross-platform compatibility
"""

import os
import re
import platform
from typing import Dict, Tuple
from pathlib import Path

def clean_filename(text: str, max_length: int = 160) -> str:
    """
    Clean filename for Windows and cross-platform compatibility
    
    Args:
        text: Original text
        max_length: Maximum filename length
        
    Returns:
        str: Clean filename
    """
    if not text:
        return "untitled"
    
    # Remove or replace invalid characters for Windows
    # Invalid chars: < > : " | ? * \ /
    invalid_chars = r'[<>:"|?*\\/]'
    cleaned = re.sub(invalid_chars, '_', text)
    
    # Replace multiple spaces with single space
    cleaned = re.sub(r'\s+', ' ', cleaned)
    
    # Remove leading/trailing dots and spaces (Windows doesn't like them)
    cleaned = cleaned.strip('. ')
    
    # Truncate to max length
    if len(cleaned) > max_length:
        cleaned = cleaned[:max_length].rstrip('. ')
    
    # Ensure not empty
    if not cleaned:
        cleaned = "untitled"
    
    return cleaned

def generate_book_filename(book: Dict, extension: str = None) -> str:
    """
    Generate consistent filename for book
    Format: "Title - Author.extension"
    
    Args:
        book: Book metadata dictionary
        extension: File extension (optional, uses book extension)
        
    Returns:
        str: Generated filename
    """
    title = book.get('title', 'Unknown Title')
    authors = book.get('authors', [])
    
    # Handle authors - can be list or string
    if isinstance(authors, list):
        author_str = ', '.join(authors[:2])  # Max 2 authors
        if len(authors) > 2:
            author_str += ' et al'
    else:
        author_str = str(authors) if authors else 'Unknown Author'
    
    # Create base filename
    if author_str and author_str != 'Unknown Author':
        filename_base = f"{title} - {author_str}"
    else:
        filename_base = title
    
    # Clean filename
    filename_base = clean_filename(filename_base, max_length=140)  # Leave room for extension
    
    # Add extension
    ext = extension or book.get('extension', 'pdf')
    if not ext.startswith('.'):
        ext = f".{ext}"
    
    return f"{filename_base}{ext.lower()}"

def generate_cover_filename(book: Dict) -> str:
    """
    Generate cover filename (same base name as book with .jpg extension)
    
    Args:
        book: Book metadata dictionary
        
    Returns:
        str: Cover filename
    """
    book_filename = generate_book_filename(book)
    
    # Replace extension with .jpg
    base_name = os.path.splitext(book_filename)[0]
    return f"{base_name}.jpg"

def ensure_directory(path: str) -> str:
    """
    Ensure directory exists, create if needed
    
    Args:
        path: Directory path
        
    Returns:
        str: Absolute path to directory
    """
    abs_path = os.path.abspath(path)
    Path(abs_path).mkdir(parents=True, exist_ok=True)
    return abs_path

def get_file_extension_from_url(url: str) -> str:
    """
    Extract file extension from URL
    
    Args:
        url: File URL
        
    Returns:
        str: File extension
    """
    try:
        # Parse URL and get the path
        from urllib.parse import urlparse, unquote
        parsed = urlparse(url)
        path = unquote(parsed.path)
        
        # Get extension
        ext = os.path.splitext(path)[1]
        if ext:
            return ext.lower()
    except:
        pass
    
    return '.jpg'  # Default for covers

def is_windows() -> bool:
    """
    Check if running on Windows
    
    Returns:
        bool: True if Windows
    """
    return platform.system().lower() == 'windows'

def get_safe_path(*args) -> str:
    """
    Join path components safely for current OS
    
    Args:
        *args: Path components
        
    Returns:
        str: Safe path
    """
    return os.path.join(*args)

def normalize_path(path: str) -> str:
    """
    Normalize path for current OS
    
    Args:
        path: Path to normalize
        
    Returns:
        str: Normalized path
    """
    return os.path.normpath(path)

def get_file_size_mb(filepath: str) -> float:
    """
    Get file size in MB
    
    Args:
        filepath: Path to file
        
    Returns:
        float: File size in MB
    """
    try:
        size_bytes = os.path.getsize(filepath)
        return round(size_bytes / (1024 * 1024), 2)
    except:
        return 0.0

def validate_filename(filename: str) -> Tuple[bool, str]:
    """
    Validate filename for current OS
    
    Args:
        filename: Filename to validate
        
    Returns:
        Tuple[bool, str]: (is_valid, error_message)
    """
    if not filename:
        return False, "Filename cannot be empty"
    
    # Check length
    if len(filename) > 255:
        return False, "Filename too long (max 255 characters)"
    
    # Check for invalid characters
    if is_windows():
        invalid_chars = r'[<>:"|?*\\/]'
        if re.search(invalid_chars, filename):
            return False, "Filename contains invalid characters"
        
        # Check for reserved names on Windows
        reserved_names = [
            'CON', 'PRN', 'AUX', 'NUL',
            'COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
            'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9'
        ]
        name_base = os.path.splitext(filename)[0].upper()
        if name_base in reserved_names:
            return False, f"Filename '{name_base}' is reserved on Windows"
    
    return True, ""

# Example usage and testing
if __name__ == "__main__":
    # Test data
    test_book = {
        'title': 'Python Programming: An Introduction to Computer Science',
        'authors': ['John Zelle', 'Another Author'],
        'extension': 'pdf'
    }
    
    # Test filename generation
    book_filename = generate_book_filename(test_book)
    cover_filename = generate_cover_filename(test_book)
    
    print(f"Book filename: {book_filename}")
    print(f"Cover filename: {cover_filename}")
    print(f"Running on Windows: {is_windows()}")
    
    # Test filename validation
    is_valid, error = validate_filename(book_filename)
    print(f"Filename valid: {is_valid}")
    if not is_valid:
        print(f"Error: {error}")