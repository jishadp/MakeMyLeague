#!/bin/bash

# Set correct permissions for Laravel project
echo "Setting permissions for Laravel project..."

# Make sure the script is run as root
if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

# Define variables
WEBUSER="www-data"
WEBGROUP="www-data"
APP_DIR="/var/www/league"

# Change ownership of all files to web user
echo "Changing ownership of files to $WEBUSER:$WEBGROUP"
chown -R $WEBUSER:$WEBGROUP $APP_DIR

# Set directory permissions
echo "Setting directory permissions..."
find $APP_DIR -type d -exec chmod 755 {} \;

# Set file permissions
echo "Setting file permissions..."
find $APP_DIR -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache directories writable
echo "Making storage and bootstrap/cache directories writable..."
chmod -R 775 $APP_DIR/storage
chmod -R 775 $APP_DIR/bootstrap/cache

# Make artisan and shell scripts executable
echo "Making artisan and shell scripts executable..."
chmod +x $APP_DIR/artisan
find $APP_DIR -name "*.sh" -exec chmod +x {} \;

echo "Permissions have been set successfully!"
