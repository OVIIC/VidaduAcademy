# DigitalOcean Deployment Guide (Docker + PostgreSQL)

This guide will help you deploy and maintain **VidaduAcademy** on a **DigitalOcean** Droplet using Docker Compose.

## Prerequisites

1.  **DigitalOcean Account**: Create a new Droplet.
2.  **Domain Name**: Point your domain's A record to your Droplet's IP address.
3.  **SSH Client**: Terminal on macOS/Linux or PowerShell/PuTTY on Windows.

---

## Step 1: Create the Droplet

1.  **Login to DigitalOcean**.
2.  Click **Create -> Droplets**.
3.  **Region**: Choose the one closest to your users (e.g., Frankfurt, London).
4.  **Image**: **Ubuntu 24.04 (LTS)** (Recommended).
5.  **Size**:
    - **Basic** -> **Regular** Disk Type.
    - **2GB RAM / 1 CPU** ($12/mo) - **Recommended minimum**.
6.  **Authentication**: Select **SSH Key** (Recommended).
7.  **Hostname**: `vidadu-server` (or similar).
8.  **Click Create Droplet**.

---

## Step 2: Server Setup

1.  **SSH into your server**:

    ```bash
    ssh root@YOUR_DROPLET_IP
    ```

2.  **Install Docker & Docker Compose**:

    ```bash
    # Update packages
    apt update && apt upgrade -y

    # Install Docker
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh

    # Setup Swap Memory (Essential for 2GB RAM)
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
    ```

---

## Step 2.5: Security Hardening (Firewall)

Protect your server by only allowing necessary traffic.

1.  **Check UFW Status**:

    ```bash
    ufw status
    ```

2.  **Allow SSH & Web Traffic**:
    _CRITICAL: Do not enable UFW without allowing SSH first, or you will be locked out._

    ```bash
    ufw allow OpenSSH
    ufw allow 80/tcp
    ufw allow 443/tcp
    ```

3.  **Enable Firewall**:
    ```bash
    ufw enable
    # Type 'y' to confirm
    ```

---

## Step 3: Application Deployment

1.  **Clone the Repository**:

    ```bash
    mkdir -p /var/www
    cd /var/www
    git clone https://github.com/OVIIC/VidaduAcademy.git app
    cd app
    ```

2.  **Environment Configuration**:

    **Backend .env**:

    ```bash
    cp backend/.env.production.example backend/.env
    nano backend/.env
    ```

    _Update `APP_URL`, `DB_PASSWORD`, `FRONTEND_URL`, `SANCTUM_STATEFUL_DOMAINS`._

    **Docker .env**:

    ```bash
    nano .env
    ```

    _Add deployment variables:_

    ```ini
    DOMAIN_NAME=vidaduacademy.com
    DB_DATABASE=vidadu_academy
    DB_USERNAME=vidadu_user
    DB_PASSWORD=YOUR_SECURE_PASSWORD
    ```

3.  **Configure Caddy (Web Server)**:
    ```bash
    nano Caddyfile
    ```
    _Replace the top domain with your actual domain name._

---

## Step 4: First Launch

1.  **Start the containers**:

    ```bash
    docker compose -f docker-compose.prod.yml up -d --build
    ```

2.  **Run Initial Setup**:

    ```bash
    # Optimize
    docker compose -f docker-compose.prod.yml exec app composer install --optimize-autoloader --no-dev
    docker compose -f docker-compose.prod.yml exec app php artisan key:generate
    docker compose -f docker-compose.prod.yml exec app php artisan storage:link

    # Database
    docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

    # Permissions
    docker compose -f docker-compose.prod.yml exec app php artisan db:seed --class=RolePermissionSeeder
    ```

---

## Maintenance & Operations

### 🔄 How to Deploy Updates

When you push new code to GitHub, run this on the server:

```bash
cd /var/www/app

# 1. Get new code
git pull

# 2. Rebuild containers (if needed) and restart
docker compose -f docker-compose.prod.yml up -d --build --force-recreate

# 3. Clear Caches (CRITICAL for this app)
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
```

### 🔒 Security Verification

**Check APP_DEBUG:**
Ensure your production app is not exposing sensitive debug info.

```bash
docker compose -f docker-compose.prod.yml exec app grep APP_DEBUG .env
# Output should be: APP_DEBUG=false
```

If it is `true`, edit `backend/.env` and change it to `false`, then restart the app container.

### 👥 Managing Admin Users

**Create a new Admin via Command Line:**

```bash
# 1. Create user
docker compose -f docker-compose.prod.yml exec app php artisan make:filament-user

# 2. Assign role
docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute="App\Models\User::where('email', 'EMAIL_HERE')->first()->assignRole('admin');"
```

### 🛠 Troubleshooting

**View Logs:**

```bash
docker compose -f docker-compose.prod.yml logs --tail=50 -f app
```

**Fix "500 Server Error" or "403 Forbidden":**
Usually a cache or permission issue. Run:

```bash
docker compose -f docker-compose.prod.yml exec app php artisan optimize:clear
docker compose -f docker-compose.prod.yml exec app php artisan permission:cache-reset
docker compose -f docker-compose.prod.yml restart app
```

**Database Access:**
To enter the database manually:

```bash
docker compose -f docker-compose.prod.yml exec postgres psql -U vidadu_user -d vidadu_academy
```
