# Uploads Directory

This directory contains uploaded project images from the admin panel.

## Security Features:
- Only image files (JPG, JPEG, PNG, GIF, WEBP) are allowed
- PHP and script execution is disabled
- Directory browsing is disabled
- Proper MIME types are set

## File Naming:
- Files are automatically renamed with unique identifiers
- Format: `project_[unique_id].[extension]`

## Access:
- Images are accessible via: `assets/uploads/filename.ext`
- Used automatically by the portfolio system
