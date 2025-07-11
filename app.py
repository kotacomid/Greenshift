#!/usr/bin/env python3
"""
Z-Library Automation Web Application
Flask web interface for Z-Library automation workflow
Suitable for deployment on cPanel
"""

import asyncio
import os
import json
from datetime import datetime
from typing import Dict, List, Optional
import threading
from concurrent.futures import ThreadPoolExecutor
import time

from flask import Flask, render_template_string, request, jsonify, send_file, redirect, url_for, session
from werkzeug.utils import secure_filename

from config import Config
from zlibrary_manager import ZLibraryManager
from csv_manager import CSVManager
from drive_manager import DriveManager
from html_generator import HTMLGenerator

# Initialize Flask app
app = Flask(__name__)
app.secret_key = Config.FLASK_SECRET_KEY
app.config['MAX_CONTENT_LENGTH'] = 16 * 1024 * 1024  # 16MB max file size

# Global variables for managers
zlib_manager = None
csv_manager = None
drive_manager = None
html_generator = None
app_status = {
    'initialized': False,
    'zlibrary_authenticated': False,
    'drive_authenticated': False,
    'last_activity': datetime.now().isoformat()
}

# Web templates
WEB_TEMPLATE = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Z-Library Automation Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard {
            padding: 20px 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        .btn-success {
            background: linear-gradient(135deg, #00b894 0%, #00a085 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        .btn-warning {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-online {
            background: #00b894;
            color: white;
        }
        .status-offline {
            background: #d63031;
            color: white;
        }
        .status-warning {
            background: #fdcb6e;
            color: #333;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #ddd;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .progress {
            height: 25px;
            border-radius: 15px;
        }
        .log-container {
            max-height: 400px;
            overflow-y: auto;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .nav-link:hover {
            color: white !important;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-book"></i> Z-Library Automation</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="/"><i class="fas fa-home"></i> Dashboard</a>
                <a class="nav-link" href="/books"><i class="fas fa-list"></i> Books</a>
                <a class="nav-link" href="/stats"><i class="fas fa-chart-bar"></i> Statistics</a>
            </div>
        </div>
    </nav>

    <div class="container dashboard">
        {{ content }}
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh functions
        function updateStatus() {
            fetch('/api/status')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('zlibrary-status').innerHTML = data.zlibrary_authenticated ? 
                        '<span class="status-badge status-online">Connected</span>' : 
                        '<span class="status-badge status-offline">Disconnected</span>';
                    
                    document.getElementById('drive-status').innerHTML = data.drive_authenticated ? 
                        '<span class="status-badge status-online">Connected</span>' : 
                        '<span class="status-badge status-offline">Disconnected</span>';
                })
                .catch(error => console.error('Error updating status:', error));
        }

        // Update status every 30 seconds
        setInterval(updateStatus, 30000);

        // Progress tracking for long operations
        function trackProgress(operationId) {
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            
            const interval = setInterval(() => {
                fetch(`/api/progress/${operationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.completed) {
                            clearInterval(interval);
                            progressBar.style.width = '100%';
                            progressText.textContent = 'Completed';
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            progressBar.style.width = data.progress + '%';
                            progressText.textContent = data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Error tracking progress:', error);
                        clearInterval(interval);
                    });
            }, 1000);
        }
    </script>
</body>
</html>
"""

def async_to_sync(coro):
    """Helper function to run async functions in sync context"""
    loop = asyncio.new_event_loop()
    asyncio.set_event_loop(loop)
    try:
        return loop.run_until_complete(coro)
    finally:
        loop.close()

def initialize_app():
    """Initialize the application components"""
    global zlib_manager, csv_manager, drive_manager, html_generator, app_status
    
    try:
        # Initialize managers
        zlib_manager = ZLibraryManager()
        csv_manager = CSVManager()
        drive_manager = DriveManager()
        html_generator = HTMLGenerator(csv_manager)
        
        # Try to authenticate
        if Config.ZLIBRARY_EMAIL and Config.ZLIBRARY_PASSWORD:
            try:
                success = async_to_sync(zlib_manager.login())
                app_status['zlibrary_authenticated'] = success
            except Exception as e:
                print(f"Z-Library authentication failed: {e}")
        
        # Try Google Drive authentication
        try:
            app_status['drive_authenticated'] = drive_manager.authenticate()
        except Exception as e:
            print(f"Google Drive authentication failed: {e}")
        
        app_status['initialized'] = True
        app_status['last_activity'] = datetime.now().isoformat()
        
    except Exception as e:
        print(f"Initialization failed: {e}")
        app_status['initialized'] = False

@app.route('/')
def dashboard():
    """Main dashboard"""
    if not app_status['initialized']:
        initialize_app()
    
    stats = csv_manager.get_statistics() if csv_manager else {'total_books': 0}
    
    content = f"""
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-tachometer-alt"></i> System Status</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Z-Library:</strong> <span id="zlibrary-status">
                                {'<span class="status-badge status-online">Connected</span>' if app_status['zlibrary_authenticated'] else '<span class="status-badge status-offline">Disconnected</span>'}
                            </span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Google Drive:</strong> <span id="drive-status">
                                {'<span class="status-badge status-online">Connected</span>' if app_status['drive_authenticated'] else '<span class="status-badge status-offline">Disconnected</span>'}
                            </span></p>
                        </div>
                    </div>
                    <p><strong>Last Activity:</strong> {app_status['last_activity']}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{stats.get('total_books', 0)}</h3>
                    <p>Total Books</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{stats.get('downloaded_books', 0)}</h3>
                    <p>Downloaded</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{stats.get('books_with_drive_links', 0)}</h3>
                    <p>In Drive</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-search"></i> Search & Download</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="/search">
                        <div class="mb-3">
                            <label for="query" class="form-label">Search Query</label>
                            <input type="text" class="form-control" id="query" name="query" placeholder="Enter book title, author, or keywords..." required>
                        </div>
                        <div class="mb-3">
                            <label for="count" class="form-label">Number of Books</label>
                            <input type="number" class="form-control" id="count" name="count" value="10" min="1" max="50">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Search Books
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-cogs"></i> Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/download" class="btn btn-success">
                            <i class="fas fa-download"></i> Download Pending Books
                        </a>
                        <a href="/upload" class="btn btn-warning">
                            <i class="fas fa-cloud-upload-alt"></i> Upload to Drive
                        </a>
                        <a href="/generate-html" class="btn btn-primary">
                            <i class="fas fa-file-code"></i> Generate HTML Catalog
                        </a>
                        <a href="/view-catalog" class="btn btn-success" target="_blank">
                            <i class="fas fa-eye"></i> View Catalog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-list"></i> Recent Books</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Authors</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {''.join([f'''
                                <tr>
                                    <td>{book.get('title', 'Unknown')[:50]}...</td>
                                    <td>{book.get('authors', 'Unknown')[:30]}...</td>
                                    <td>
                                        <span class="status-badge status-{'online' if book.get('download_status') == 'completed' else 'warning' if book.get('download_status') == 'pending' else 'offline'}">
                                            {book.get('download_status', 'Unknown')}
                                        </span>
                                    </td>
                                    <td>
                                        {f'<a href="{book.get("drive_link")}" class="btn btn-sm btn-success" target="_blank">View</a>' if book.get('drive_link') else ''}
                                    </td>
                                </tr>
                                ''' for book in (csv_manager.get_all_books()[:10] if csv_manager else [])])}
                            </tbody>
                        </table>
                    </div>
                    <a href="/books" class="btn btn-primary">View All Books</a>
                </div>
            </div>
        </div>
    </div>
    """
    
    return render_template_string(WEB_TEMPLATE, content=content)

@app.route('/search', methods=['POST'])
def search_books():
    """Search for books and add to CSV"""
    if not app_status['zlibrary_authenticated']:
        return jsonify({'error': 'Z-Library not authenticated'}), 400
    
    query = request.form.get('query', '').strip()
    count = int(request.form.get('count', 10))
    
    if not query:
        return jsonify({'error': 'Query is required'}), 400
    
    try:
        # Run search in background
        books = async_to_sync(zlib_manager.search_books(query, count))
        added_count = csv_manager.add_books(books)
        
        app_status['last_activity'] = datetime.now().isoformat()
        
        return redirect(url_for('dashboard'))
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/download')
def download_books():
    """Download pending books"""
    if not app_status['zlibrary_authenticated']:
        return jsonify({'error': 'Z-Library not authenticated'}), 400
    
    try:
        # Get pending books
        pending_books = csv_manager.get_books_by_status('pending')
        
        if not pending_books:
            return redirect(url_for('dashboard'))
        
        # Download books (limit to first 5 to avoid timeout)
        downloaded_count = 0
        for book in pending_books[:5]:
            try:
                csv_manager.update_book_status(book['id'], 'downloading')
                download_result = async_to_sync(zlib_manager.download_book(book))
                
                if download_result['success']:
                    update_data = {'local_path': download_result['book_path']}
                    if download_result['cover_path']:
                        update_data['cover_local_path'] = download_result['cover_path']
                    
                    csv_manager.update_book_status(book['id'], 'completed', **update_data)
                    downloaded_count += 1
                else:
                    csv_manager.update_book_status(book['id'], 'error')
            except Exception as e:
                csv_manager.update_book_status(book['id'], 'error')
                print(f"Download error: {e}")
        
        app_status['last_activity'] = datetime.now().isoformat()
        return redirect(url_for('dashboard'))
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/upload')
def upload_to_drive():
    """Upload completed books to Google Drive"""
    if not app_status['drive_authenticated']:
        return jsonify({'error': 'Google Drive not authenticated'}), 400
    
    try:
        # Get completed books without drive links
        completed_books = csv_manager.get_books_by_status('completed')
        books_to_upload = [book for book in completed_books 
                          if book.get('local_path') and os.path.exists(book['local_path'])
                          and not book.get('drive_link')]
        
        if not books_to_upload:
            return redirect(url_for('dashboard'))
        
        # Upload books (limit to first 3 to avoid timeout)
        uploaded_count = 0
        for book in books_to_upload[:3]:
            try:
                from utils import generate_book_filename, generate_cover_filename
                
                book_path = book['local_path']
                cover_path = book.get('cover_local_path', '')
                
                book_filename = generate_book_filename(book)
                cover_filename = generate_cover_filename(book) if cover_path else None
                
                upload_result = drive_manager.upload_book_and_cover(
                    book_path=book_path,
                    cover_path=cover_path if cover_path and os.path.exists(cover_path) else None,
                    book_name=book_filename,
                    cover_name=cover_filename
                )
                
                if upload_result['success']:
                    update_data = {'drive_link': upload_result['book_link']}
                    if upload_result['cover_link']:
                        update_data['cover_drive_link'] = upload_result['cover_link']
                    
                    csv_manager.update_book_status(book['id'], 'completed', **update_data)
                    uploaded_count += 1
            except Exception as e:
                print(f"Upload error: {e}")
        
        app_status['last_activity'] = datetime.now().isoformat()
        return redirect(url_for('dashboard'))
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/generate-html')
def generate_html():
    """Generate HTML catalog"""
    try:
        html_path = html_generator.generate_catalog_html()
        app_status['last_activity'] = datetime.now().isoformat()
        return redirect(url_for('dashboard'))
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/view-catalog')
def view_catalog():
    """View the generated HTML catalog"""
    try:
        if os.path.exists(Config.HTML_OUTPUT_PATH):
            return send_file(Config.HTML_OUTPUT_PATH)
        else:
            # Generate if doesn't exist
            html_generator.generate_catalog_html()
            return send_file(Config.HTML_OUTPUT_PATH)
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/books')
def list_books():
    """List all books"""
    books = csv_manager.get_all_books() if csv_manager else []
    
    content = f"""
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-book"></i> All Books ({len(books)})</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Authors</th>
                            <th>Year</th>
                            <th>Format</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {''.join([f'''
                        <tr>
                            <td>{book.get('title', 'Unknown')[:50]}...</td>
                            <td>{book.get('authors', 'Unknown')[:30]}...</td>
                            <td>{book.get('year', 'N/A')}</td>
                            <td>{book.get('extension', 'N/A')}</td>
                            <td>
                                <span class="status-badge status-{'online' if book.get('download_status') == 'completed' else 'warning' if book.get('download_status') == 'pending' else 'offline'}">
                                    {book.get('download_status', 'Unknown')}
                                </span>
                            </td>
                            <td>
                                {f'<a href="{book.get("drive_link")}" class="btn btn-sm btn-success" target="_blank">Drive</a>' if book.get('drive_link') else ''}
                                {f'<a href="{book.get("url")}" class="btn btn-sm btn-primary" target="_blank">Z-Lib</a>' if book.get('url') else ''}
                            </td>
                        </tr>
                        ''' for book in books])}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    """
    
    return render_template_string(WEB_TEMPLATE, content=content)

@app.route('/stats')
def show_stats():
    """Show statistics"""
    stats = csv_manager.get_statistics() if csv_manager else {}
    
    content = f"""
    <div class="row">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{stats.get('total_books', 0)}</h3>
                    <p>Total Books</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{stats.get('downloaded_books', 0)}</h3>
                    <p>Downloaded</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{stats.get('books_with_drive_links', 0)}</h3>
                    <p>In Drive</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{len(stats.get('language_counts', {}))}</h3>
                    <p>Languages</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Status Distribution</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {''.join([f'<li class="list-group-item d-flex justify-content-between">{status}<span class="badge bg-primary">{count}</span></li>' for status, count in stats.get('status_counts', {}).items()])}
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Language Distribution</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        {''.join([f'<li class="list-group-item d-flex justify-content-between">{lang}<span class="badge bg-success">{count}</span></li>' for lang, count in list(stats.get('language_counts', {}).items())[:10]])}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    """
    
    return render_template_string(WEB_TEMPLATE, content=content)

@app.route('/api/status')
def api_status():
    """API endpoint for status"""
    return jsonify(app_status)

@app.route('/api/stats')
def api_stats():
    """API endpoint for statistics"""
    stats = csv_manager.get_statistics() if csv_manager else {}
    return jsonify(stats)

@app.errorhandler(404)
def not_found(error):
    return redirect(url_for('dashboard'))

@app.errorhandler(500)
def internal_error(error):
    return jsonify({'error': 'Internal server error'}), 500

# Initialize the app when module is loaded
initialize_app()

if __name__ == '__main__':
    # For development
    app.run(
        host=Config.FLASK_HOST,
        port=Config.FLASK_PORT,
        debug=Config.FLASK_DEBUG
    )
else:
    # For production (cPanel)
    # This is the WSGI application object
    application = app