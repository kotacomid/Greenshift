import os
import pickle
from typing import Optional, Dict
from googleapiclient.discovery import build
from googleapiclient.http import MediaFileUpload
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request
from config import Config

class DriveManager:
    """Manager class for Google Drive operations"""
    
    # Google Drive API scopes
    SCOPES = ['https://www.googleapis.com/auth/drive.file']
    
    def __init__(self):
        self.service = None
        self.credentials_file = Config.GOOGLE_CREDENTIALS_FILE
        self.token_file = Config.GOOGLE_TOKEN_FILE
        self.folder_id = Config.DRIVE_FOLDER_ID
    
    def authenticate(self) -> bool:
        """
        Authenticate with Google Drive API
        
        Returns:
            bool: True if authentication successful, False otherwise
        """
        try:
            creds = None
            
            # Load existing token
            if os.path.exists(self.token_file):
                with open(self.token_file, 'rb') as token:
                    creds = pickle.load(token)
            
            # If no valid credentials, get new ones
            if not creds or not creds.valid:
                if creds and creds.expired and creds.refresh_token:
                    creds.refresh(Request())
                else:
                    if not os.path.exists(self.credentials_file):
                        print(f"❌ Credentials file not found: {self.credentials_file}")
                        print("Please download credentials.json from Google Cloud Console")
                        return False
                    
                    flow = InstalledAppFlow.from_client_secrets_file(
                        self.credentials_file, self.SCOPES)
                    creds = flow.run_local_server(port=0)
                
                # Save credentials for next run
                with open(self.token_file, 'wb') as token:
                    pickle.dump(creds, token)
            
            self.service = build('drive', 'v3', credentials=creds)
            print("✅ Successfully authenticated with Google Drive")
            return True
            
        except Exception as e:
            print(f"❌ Failed to authenticate with Google Drive: {str(e)}")
            return False
    
    def create_folder(self, folder_name: str, parent_folder_id: str = None) -> Optional[str]:
        """
        Create a folder in Google Drive
        
        Args:
            folder_name: Name of the folder to create
            parent_folder_id: ID of parent folder (optional)
            
        Returns:
            str: Folder ID if successful, None otherwise
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return None
        
        try:
            folder_metadata = {
                'name': folder_name,
                'mimeType': 'application/vnd.google-apps.folder'
            }
            
            if parent_folder_id:
                folder_metadata['parents'] = [parent_folder_id]
            
            folder = self.service.files().create(body=folder_metadata).execute()
            folder_id = folder.get('id')
            
            print(f"✅ Created folder '{folder_name}' with ID: {folder_id}")
            return folder_id
            
        except Exception as e:
            print(f"❌ Failed to create folder: {str(e)}")
            return None
    
    def upload_file(self, file_path: str, file_name: str = None, folder_id: str = None) -> Optional[Dict]:
        """
        Upload a file to Google Drive
        
        Args:
            file_path: Local path to the file
            file_name: Name for the file in Drive (optional, uses original name)
            folder_id: ID of folder to upload to (optional)
            
        Returns:
            Dict: File metadata if successful, None otherwise
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return None
        
        if not os.path.exists(file_path):
            print(f"❌ File not found: {file_path}")
            return None
        
        try:
            file_name = file_name or os.path.basename(file_path)
            folder_id = folder_id or self.folder_id
            
            file_metadata = {'name': file_name}
            if folder_id:
                file_metadata['parents'] = [folder_id]
            
            media = MediaFileUpload(file_path, resumable=True)
            
            print(f"⬆️ Uploading file: {file_name}...")
            
            file = self.service.files().create(
                body=file_metadata,
                media_body=media,
                fields='id,name,webViewLink,webContentLink'
            ).execute()
            
            print(f"✅ Uploaded file: {file_name} (ID: {file.get('id')})")
            return file
            
        except Exception as e:
            print(f"❌ Failed to upload file: {str(e)}")
            return None
    
    def make_file_public(self, file_id: str) -> bool:
        """
        Make a file publicly accessible
        
        Args:
            file_id: ID of the file to make public
            
        Returns:
            bool: True if successful, False otherwise
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return False
        
        try:
            permission = {
                'type': 'anyone',
                'role': 'reader'
            }
            
            self.service.permissions().create(
                fileId=file_id,
                body=permission
            ).execute()
            
            print(f"✅ Made file {file_id} publicly accessible")
            return True
            
        except Exception as e:
            print(f"❌ Failed to make file public: {str(e)}")
            return False
    
    def get_file_link(self, file_id: str) -> Optional[str]:
        """
        Get shareable link for a file
        
        Args:
            file_id: ID of the file
            
        Returns:
            str: Shareable link if successful, None otherwise
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return None
        
        try:
            file = self.service.files().get(
                fileId=file_id,
                fields='webViewLink,webContentLink'
            ).execute()
            
            # Return the view link (shareable link)
            link = file.get('webViewLink')
            print(f"✅ Retrieved link for file {file_id}")
            return link
            
        except Exception as e:
            print(f"❌ Failed to get file link: {str(e)}")
            return None
    
    def upload_and_share(self, file_path: str, file_name: str = None, folder_id: str = None) -> Optional[str]:
        """
        Upload a file and get its shareable link
        
        Args:
            file_path: Local path to the file
            file_name: Name for the file in Drive (optional)
            folder_id: ID of folder to upload to (optional)
            
        Returns:
            str: Shareable link if successful, None otherwise
        """
        try:
            # Upload file
            file_metadata = self.upload_file(file_path, file_name, folder_id)
            if not file_metadata:
                return None
            
            file_id = file_metadata.get('id')
            
            # Make file public
            if self.make_file_public(file_id):
                # Get shareable link
                link = self.get_file_link(file_id)
                if link:
                    print(f"✅ File uploaded and shared: {link}")
                    return link
            
            return None
            
        except Exception as e:
            print(f"❌ Failed to upload and share file: {str(e)}")
            return None
    
    def list_files(self, folder_id: str = None, max_results: int = 10) -> list:
        """
        List files in a folder
        
        Args:
            folder_id: ID of folder to list (optional, lists root)
            max_results: Maximum number of files to return
            
        Returns:
            list: List of file metadata
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return []
        
        try:
            query = ""
            if folder_id:
                query = f"'{folder_id}' in parents"
            
            results = self.service.files().list(
                q=query,
                pageSize=max_results,
                fields="files(id, name, webViewLink, mimeType, size)"
            ).execute()
            
            files = results.get('files', [])
            print(f"✅ Found {len(files)} files")
            return files
            
        except Exception as e:
            print(f"❌ Failed to list files: {str(e)}")
            return []
    
    def delete_file(self, file_id: str) -> bool:
        """
        Delete a file from Google Drive
        
        Args:
            file_id: ID of the file to delete
            
        Returns:
            bool: True if successful, False otherwise
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return False
        
        try:
            self.service.files().delete(fileId=file_id).execute()
            print(f"✅ Deleted file {file_id}")
            return True
            
        except Exception as e:
            print(f"❌ Failed to delete file: {str(e)}")
            return False
    
    def get_folder_info(self, folder_id: str) -> Optional[Dict]:
        """
        Get information about a folder
        
        Args:
            folder_id: ID of the folder
            
        Returns:
            Dict: Folder metadata if successful, None otherwise
        """
        if not self.service:
            print("❌ Not authenticated with Google Drive")
            return None
        
        try:
            folder = self.service.files().get(
                fileId=folder_id,
                fields='id,name,webViewLink,parents'
            ).execute()
            
            print(f"✅ Retrieved info for folder: {folder.get('name')}")
            return folder
            
        except Exception as e:
            print(f"❌ Failed to get folder info: {str(e)}")
            return None

# Example usage
if __name__ == "__main__":
    # Test Drive manager
    drive_manager = DriveManager()
    
    if drive_manager.authenticate():
        # List files in root
        files = drive_manager.list_files()
        for file in files:
            print(f"File: {file['name']} (ID: {file['id']})")
        
        # Create a test folder
        folder_id = drive_manager.create_folder("Test ZLibrary Books")
        if folder_id:
            print(f"Created folder with ID: {folder_id}")
    else:
        print("Failed to authenticate with Google Drive")