#!/bin/bash

# Configuration
CONTAINER_NAME="vidadu-postgres"
DB_USER="vidadu_user"
DB_NAME="vidadu_academy"
APP_CONTAINER="vidadu-app"

# Check arguments
if [ -z "$1" ]; then
    echo "Usage: ./scripts/restore.sh <path_to_backup_tar_gz>"
    echo "Example: ./scripts/restore.sh backups/backup_20230101_120000.tar.gz"
    exit 1
fi

BACKUP_FILE="$1"

# Confirmation
echo "=========================================="
echo "⚠️  DANGER ZONE: RESTORE PROCESS"
echo "=========================================="
echo "You are about to restore: $BACKUP_FILE"
echo "This will OVERWRITE:"
echo "  1. The current database ($DB_NAME)"
echo "  2. The current .env file"
echo "  3. The current user storage files"
echo ""
read -p "Are you absolutely sure? Type 'YES' to proceed: " CONFIRM
if [ "$CONFIRM" != "YES" ]; then
    echo "❌ Restore cancelled."
    exit 1
fi

TEMP_DIR="./restore_temp_$(date +%s)"
mkdir -p "$TEMP_DIR"

echo "------------------------------------------"
echo "📂 Extracting backup archive..."
tar -xzf "$BACKUP_FILE" -C "$TEMP_DIR"

# Identify extracted folder name (it might be dynamic based on timestamp)
EXTRACTED_FOLDER=$(ls -d "$TEMP_DIR"/backup_* | head -n 1)

if [ -z "$EXTRACTED_FOLDER" ]; then
    echo "❌ Error: Could not find backup folder inside archive."
    rm -rf "$TEMP_DIR"
    exit 1
fi

echo "✅ Extracted to $EXTRACTED_FOLDER"

# 1. Restore Database
if [ -f "$EXTRACTED_FOLDER/db.sql" ]; then
    echo "🔄 Restoring Database..."
    # We drop the schema public and recreate it to ensure a clean slate, then import
    # Alternatively, just running the SQL might be enough if it contains drops, but pg_dump default usually doesn't.
    # A safer way without dropping the whole DB is dropping the schema.
    
    docker exec -i $CONTAINER_NAME psql -U $DB_USER -d $DB_NAME -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"
    
    cat "$EXTRACTED_FOLDER/db.sql" | docker exec -i $CONTAINER_NAME psql -U $DB_USER -d $DB_NAME
    echo "✅ Database restored."
else
    echo "⚠️  db.sql not found in backup. Skipping database restore."
fi

# 2. Restore .env
if [ -f "$EXTRACTED_FOLDER/.env" ]; then
    echo "🔄 Restoring .env..."
    cp "$EXTRACTED_FOLDER/.env" backend/.env
    echo "✅ .env restored."
else
    echo "⚠️  .env not found in backup. Skipping."
fi

# 3. Restore Storage
if [ -d "$EXTRACTED_FOLDER/storage" ]; then
    echo "🔄 Restoring Storage..."
    # Ensure target exists
    mkdir -p backend/storage/app/public
    # Copy files
    cp -r "$EXTRACTED_FOLDER/storage/"* backend/storage/app/public/
    echo "✅ Storage restored."
    
    # Fix permissions (User www-data inside container usually needs ownership, but locally we maintain ours)
    # On production, you might need to fix permissions after this.
    echo "   (Note: You might need to run 'chown -R www-data:www-data backend/storage' inside the container if permissions are lost)"
else
    echo "⚠️  Storage directory not found in backup. Skipping."
fi

# Cleanup
echo "🧹 Cleaning up..."
rm -rf "$TEMP_DIR"

# Clear Caches
echo "Refresh Application Cache..."
docker exec $APP_CONTAINER php artisan optimize:clear

echo "=========================================="
echo "✅ RESTORE COMPLETED SUCCESSFULLY"
echo "=========================================="
