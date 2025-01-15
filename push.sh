#!/bin/bash

# SVN repository URL
SVN_URL="https://plugins.svn.wordpress.org/floating-contacts"
PLUGIN_SLUG="floating-contacts"
VERSION=$(grep "Version:" "$PLUGIN_SLUG.php" | awk '{print $3}')
SVN_DIR="svn-$PLUGIN_SLUG"

# Prompt for SVN credentials
read -p "Enter your WordPress.org SVN username: " SVN_USERNAME
read -s -p "Enter your WordPress.org SVN password: " SVN_PASSWORD
echo ""

# Check if version is detected
if [ -z "$VERSION" ]; then
    echo "Error: Unable to detect plugin version. Make sure the 'Version:' field exists in your main plugin file."
    exit 1
fi

echo "Deploying version $VERSION to SVN..."

# Check if SVN is installed
if ! command -v svn &> /dev/null; then
    echo "Error: svn is not installed. Please install Subversion before proceeding."
    exit 1
fi

# Clean up old SVN directory if it exists
if [ -d "$SVN_DIR" ]; then
    rm -rf "$SVN_DIR"
fi

# Checkout the latest SVN repository
echo "Checking out SVN repository..."
svn checkout --username "$SVN_USERNAME" --password "$SVN_PASSWORD" "$SVN_URL" "$SVN_DIR"

# Remove all files from trunk except .svn (SVN metadata)
echo "Cleaning up trunk directory..."
rm -rf "$SVN_DIR/trunk/*"

# Copy plugin files to the trunk directory, excluding unwanted files
echo "Copying plugin files to trunk..."
rsync -av --exclude="node_modules" --exclude=".git" --exclude=".github" --exclude=".vscode" --exclude=".gitignore" \
      --exclude=".wordpress-org" --exclude="*.log" --exclude="build.sh" --exclude="*.zip" --exclude="push.sh" \
      ./ "$SVN_DIR/trunk/"

# Check if assets directory exists and copy it
if [ -d "assets" ]; then
    echo "Copying assets to SVN..."
    rsync -av svn-assets/ "$SVN_DIR/assets/"
    
    # Set MIME types for images in assets
    echo "Setting MIME types for images..."
    svn propset svn:mime-type image/png "$SVN_DIR/assets/*.png" 2>/dev/null
    svn propset svn:mime-type image/jpeg "$SVN_DIR/assets/*.jpg" 2>/dev/null
fi

# Navigate to the SVN directory
cd "$SVN_DIR" || { echo "Error: Failed to navigate to SVN directory."; exit 1; }

# Add all new files to SVN
echo "Adding new files to SVN..."
svn add --force trunk/* assets/*

# Check for any changes and commit
echo "Committing changes to SVN..."
svn commit --username "$SVN_USERNAME" --password "$SVN_PASSWORD" -m "Deploying version $VERSION of $PLUGIN_SLUG"

# Tag the new version
echo "Tagging version $VERSION..."
svn copy --username "$SVN_USERNAME" --password "$SVN_PASSWORD" trunk tags/"$VERSION"
svn commit --username "$SVN_USERNAME" --password "$SVN_PASSWORD" -m "Tagging version $VERSION"

echo "Plugin version $VERSION successfully deployed!"
