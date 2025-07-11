#!/usr/bin/env python3
"""
Test script untuk menguji file naming dan Windows compatibility
"""

import os
import sys

# Add current directory to path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from utils import (
    generate_book_filename, 
    generate_cover_filename, 
    clean_filename,
    validate_filename,
    is_windows
)

def test_book_naming():
    """Test book filename generation"""
    print("🧪 Testing Book Filename Generation")
    print("=" * 50)
    
    test_cases = [
        {
            'title': 'Python Programming: An Introduction to Computer Science',
            'authors': ['John Zelle'],
            'extension': 'pdf'
        },
        {
            'title': 'The Complete Guide to Machine Learning with Python & TensorFlow',
            'authors': ['Alice Smith', 'Bob Johnson', 'Charlie Brown'],
            'extension': 'epub'
        },
        {
            'title': 'Data Science <>&:"|?*\\/  Book',
            'authors': ['Invalid/Chars\\Author'],
            'extension': 'pdf'
        },
        {
            'title': 'A' * 200,  # Very long title
            'authors': ['B' * 100],  # Very long author
            'extension': 'mobi'
        },
        {
            'title': 'Simple Book',
            'authors': [],  # No authors
            'extension': 'pdf'
        },
        {
            'title': 'Book with: Special? Characters* and <Others>',
            'authors': 'Single Author String',  # String instead of list
            'extension': 'txt'
        }
    ]
    
    for i, book in enumerate(test_cases, 1):
        print(f"\n📚 Test Case {i}:")
        print(f"   Title: {book['title'][:50]}{'...' if len(book['title']) > 50 else ''}")
        print(f"   Authors: {book['authors']}")
        print(f"   Extension: {book['extension']}")
        
        # Generate filenames
        book_filename = generate_book_filename(book)
        cover_filename = generate_cover_filename(book)
        
        print(f"   📄 Book file: {book_filename}")
        print(f"   📸 Cover file: {cover_filename}")
        print(f"   📏 Book length: {len(book_filename)} chars")
        print(f"   📏 Cover length: {len(cover_filename)} chars")
        
        # Validate filenames
        book_valid, book_error = validate_filename(book_filename)
        cover_valid, cover_error = validate_filename(cover_filename)
        
        print(f"   ✅ Book valid: {book_valid}")
        if not book_valid:
            print(f"      Error: {book_error}")
        
        print(f"   ✅ Cover valid: {cover_valid}")
        if not cover_valid:
            print(f"      Error: {cover_error}")

def test_special_cases():
    """Test special cases and edge conditions"""
    print("\n\n🧪 Testing Special Cases")
    print("=" * 50)
    
    special_cases = [
        "",  # Empty string
        "   ",  # Whitespace only
        "CON",  # Windows reserved name
        "con.txt",  # Windows reserved with extension
        "file.with.many.dots.pdf",  # Multiple dots
        "file   with   spaces.pdf",  # Multiple spaces
        "normalfile.pdf",  # Normal case
        ".hidden",  # Hidden file (starts with dot)
        "file.",  # Ends with dot
        "file..pdf",  # Double dots
    ]
    
    for case in special_cases:
        print(f"\n📝 Testing: '{case}'")
        cleaned = clean_filename(case)
        is_valid, error = validate_filename(cleaned)
        
        print(f"   🧹 Cleaned: '{cleaned}'")
        print(f"   ✅ Valid: {is_valid}")
        if not is_valid:
            print(f"      Error: {error}")

def test_platform_info():
    """Test platform detection"""
    print("\n\n🧪 Platform Information")
    print("=" * 50)
    
    print(f"🖥️ Running on Windows: {is_windows()}")
    print(f"🐍 Python version: {sys.version}")
    print(f"📁 Current directory: {os.getcwd()}")
    print(f"🛤️ Path separator: '{os.sep}'")

def create_test_files():
    """Create test files to verify naming works"""
    print("\n\n🧪 Creating Test Files")
    print("=" * 50)
    
    test_book = {
        'title': 'Test Book for Naming System',
        'authors': ['Test Author'],
        'extension': 'pdf'
    }
    
    book_filename = generate_book_filename(test_book)
    cover_filename = generate_cover_filename(test_book)
    
    # Create test directory
    test_dir = "test_files"
    os.makedirs(test_dir, exist_ok=True)
    
    # Create test files
    book_path = os.path.join(test_dir, book_filename)
    cover_path = os.path.join(test_dir, cover_filename)
    
    try:
        with open(book_path, 'w') as f:
            f.write("This is a test book file.")
        
        with open(cover_path, 'w') as f:
            f.write("This is a test cover file.")
        
        print(f"✅ Created test book: {book_path}")
        print(f"✅ Created test cover: {cover_path}")
        print(f"📁 Files created in: {os.path.abspath(test_dir)}")
        
        # Verify files exist
        if os.path.exists(book_path) and os.path.exists(cover_path):
            print("✅ Both files exist and are accessible")
        else:
            print("❌ File creation verification failed")
            
    except Exception as e:
        print(f"❌ Error creating test files: {e}")

def main():
    """Main test function"""
    print("🚀 Z-Library File Naming System Test")
    print("🔧 Testing Windows compatibility and file naming")
    print("=" * 60)
    
    # Run all tests
    test_platform_info()
    test_book_naming()
    test_special_cases()
    create_test_files()
    
    print("\n" + "=" * 60)
    print("✅ Testing completed!")
    print("📝 Check the generated filenames above to ensure they're appropriate")
    print("🗂️ Test files created in 'test_files' directory")

if __name__ == "__main__":
    main()