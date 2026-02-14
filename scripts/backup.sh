#!/bin/bash

# Configuration
BACKUP_ROOT="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_NAME="backup_$TIMESTAMP"
BACKUP_PATH="$BACKUP_ROOT/$BACKUP_NAME"

# Docker Configuration
# Ensure these match your docker-compose.prod.yml
CONTAINER_NAME="vidadu-postgres"
DB_USER="vidadu_user"
DB_NAME="vidadu_academy"

# Ensure backup directory exists
mkdir -p "$BACKUP_PATH"

echo "=========================================="
echo "📦 VidaduAcademy Backup Started: $TIMESTAMP"
echo "=========================================="

# 1. Backup Database
echo "Running: Dumping PostgreSQL database..."
if docker exec -t $CONTAINER_NAME pg_dump -U $DB_USER -d $DB_NAME > "$BACKUP_PATH/db.sql"; then
    echo "✅ Database dump successful."
else
    echo "❌ Database dump FAILED. Is the container running?"
    rm -rf "$BACKUP_PATH"
    exit 1
fi

# 2. Backup Environment Variables
echo "Running: Backing up .env file..."
if [ -f backend/.env ]; then
    cp backend/.env "$BACKUP_PATH/.env"
    echo "✅ .env file backed up."
else
    echo "⚠️  backend/.env not found, skipping."
fi

# 3. Backup Storage (User Uploads)
echo "Running: Backing up public storage..."
if [ -d backend/storage/app/public ]; then
    mkdir -p "$BACKUP_PATH/storage"
    cp -r backend/storage/app/public/* "$BACKUP_PATH/storage/"
    echo "✅ Storage backed up."
else
    echo "⚠️  backend/storage/app/public not found, skipping."
fi

# 4. Compress to Archive
echo "Running: Creating compressed archive..."
tar -czf "$BACKUP_ROOT/$BACKUP_NAME.tar.gz" -C "$BACKUP_ROOT" "$BACKUP_NAME"

# 5. Cleanup Temporary Files
rm -rf "$BACKUP_PATH"

echo "=========================================="
echo "✅ BACKUP SUCCESSFUL"
echo "📁 Location: $BACKUP_ROOT/$BACKUP_NAME.tar.gz"
echo "=========================================="
echo "💡 NEXT STEP: Download this file to your local machine!"
echo "   Run this ON YOUR LOCAL MACHINE (not server):"
echo "   scp root@<your-server-ip>:/var/www/app/backups/$BACKUP_NAME.tar.gz ./"
echo "=========================================="
