# VidaduAcademy Backup Guide

This guide explains how to manually backup and restore your VidaduAcademy installation.
Since we are not using DigitalOcean's automated backups, **you are responsible for downloading these backups to your local computer.**

---

## 🛑 Critical Warning

**If you lose your server AND you haven't downloaded a backup to your computer, your data is gone forever.**
Make it a habit to run a backup and download it:

- Before any deployment or update.
- Regularly (e.g., once a week).

---

## 1. Creating a Backup

1.  **SSH into your server**:

    ```bash
    ssh root@<your-server-ip>
    ```

2.  **Go to the app directory**:

    ```bash
    cd /var/www/app
    ```

3.  **Run the backup script**:

    ```bash
    ./scripts/backup.sh
    ```

    This will create a file like: `backups/backup_20231027_103000.tar.gz`

---

## 2. Downloading the Backup (Off-site Storage)

**Do this on your LOCAL computer (Mac), NOT on the server.**

1.  Open a new terminal window on your Mac.
2.  Run the following command (replace with your actual IP):

    ```bash
    # Syntax: scp root@IP:/path/to/remote/file ./local/path
    scp root@<your-server-ip>:/var/www/app/backups/backup_YYYYMMDD_HHMMSS.tar.gz ./Desktop/
    ```

    _Example:_
    `scp root@168.12.34.56:/var/www/app/backups/backup_20231027_103000.tar.gz ~/Desktop/`

Now the backup file is safe on your Desktop.

---

## 3. Restoring from a Backup

If your server crashes or you want to revert to a previous version:

### A. Upload the backup to the server

(If the file is on your computer)

**On your LOCAL computer:**

```bash
scp ~/Desktop/backup_20231027_103000.tar.gz root@<your-server-ip>:/var/www/app/backups/
```

### B. Run the Restore Script

**On the SERVER:**

1.  SSH into the server.
2.  Navigate to the app folder:
    ```bash
    cd /var/www/app
    ```
3.  Run the restore script with the backup file:

    ```bash
    # ./scripts/restore.sh <path-to-backup-file>
    ./scripts/restore.sh backups/backup_20231027_103000.tar.gz
    ```

4.  **Type `YES`** when prompted to confirm overwriting the database.

The script will:

- Restore the database.
- Restore the `.env` file.
- Restore user uploaded files (images, videos).
- Clear caches.

---

## 4. Troubleshooting

- **Database Errors**: Ensure the Docker containers are running (`docker compose -f docker-compose.prod.yml ps`).
- **Permission Errors**: If the script says "Permission denied", run `chmod +x scripts/*.sh` to make them executable.
