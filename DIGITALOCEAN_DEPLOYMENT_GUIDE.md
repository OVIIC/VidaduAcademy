# DigitalOcean Deployment Guide (Docker + PostgreSQL)

This guide will help you deploy **VidaduAcademy** to a **DigitalOcean** Droplet (2GB RAM recommended) using Docker Compose and PostgreSQL.

## Prerequisites

1.  **DigitalOcean Account**: Create a new Droplet.
2.  **Domain Name**: Point your domain's A record to your Droplet's IP address.
3.  **SSH Client**: Terminal on macOS.

---

## Step 1: Create the Droplet

1.  **Login to DigitalOcean**.
2.  Click **Create -> Droplets**.
3.  **Region**: Choose the one closest to your users (e.g., Frankfurt, London).
4.  **Image**: **Ubuntu 22.04 (LTS)** or **24.04 (LTS)**.
5.  **Size**:
    - **Basic** -> **Regular** Disk Type.
    - **2GB RAM / 1 CPU** ($12/mo) - **Recommended**.
    - (1GB RAM is possible but risky with PostgreSQL + Composer updates).
6.  **Authentication**: Select **SSH Key** (Recommended) or Password.
7.  **Hostname**: `vidadu-server` (or whatever you like.
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

    # Verify installation
    docker compose version
    ```

3.  **Setup Swap Memory** (Essential for reliability):
    ```bash
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
    ```

---

## Step 3: Application Deployment

1.  **Clone the Repository**:

    ```bash
    mkdir -p /var/www
    cd /var/www
    git clone https://github.com/YOUR_GITHUB_USER/VidaduAcademy.git app
    cd app
    ```

2.  **Environment Configuration**:

    Create and edit your `.env` file for the **Backend** using the production template:

    ```bash
    cp backend/.env.production.example backend/.env
    nano backend/.env
    ```

    **Update these lines in `backend/.env`:**
    - `APP_URL`: Set to your domain (e.g., `https://vidadu.sk`).
    - `DB_PASSWORD`: Set a secure password.
    - `FRONTEND_URL`: Set to your domain.
    - `SANCTUM_STATEFUL_DOMAINS`: Set to your domain (without `https://`).

3.  **Configure Caddy (Web Server)**:

    ```bash
    nano Caddyfile
    ```

    Replace the top line `http://localhost:80` with your actual domain:

    ```
    your-domain.com {
        ...
    }
    ```

4.  **Update Docker Environment Variables**:
    You also need to set the database password for the Docker container itself.
    Create a `.env` file in the **root** folder (where `docker-compose.prod.yml` is):

    ```bash
    nano .env
    ```

    Add this:

    ```ini
    # Domain for App
    DOMAIN_NAME=your-domain.com

    # Database Defaults for Postgres Container
    DB_DATABASE=vidadu_academy
    DB_USERNAME=vidadu_user
    DB_PASSWORD=YOUR_SECURE_PASSWORD # MUST MATCH backend/.env
    ```

---

## Step 4: Launch

1.  **Start the containers**:

    ```bash
    docker compose -f docker-compose.prod.yml up -d --build
    ```

2.  **Run Migrations & Optimizations**:

    ```bash
    # Install dependencies (if not fully baked in image)
    docker compose -f docker-compose.prod.yml exec app composer install --optimize-autoloader --no-dev

    # Generate Key
    docker compose -f docker-compose.prod.yml exec app php artisan key:generate

    # Run Migrations
    docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

    # Optimize
    docker compose -f docker-compose.prod.yml exec app php artisan config:cache
    docker compose -f docker-compose.prod.yml exec app php artisan route:cache
    docker compose -f docker-compose.prod.yml exec app php artisan view:cache

    # Link Storage
    docker compose -f docker-compose.prod.yml exec app php artisan storage:link
    ```

---

## Step 5: Verification

1.  Visit `https://your-domain.com`.
2.  Check logs if something is wrong:
    ```bash
    docker compose -f docker-compose.prod.yml logs -f app
    ```
