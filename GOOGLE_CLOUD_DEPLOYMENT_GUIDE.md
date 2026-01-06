# Google Cloud Free Tier Deployment Guide

This guide will walk you through deploying the **VidaduAcademy** application to a **Google Cloud Platform (GCP)** **e2-micro** instance. This setup uses **SQLite** to fit within the free tier's 1GB RAM limit.

## Prerequisites

1.  **Google Cloud Account**: A GCP account (new users get $300 credit, but we will use the Always Free tier).
2.  **Domain Name**: A domain you own, compliant with Google's verification if needed, or simply pointing to the IP.
3.  **SSH Client**: Terminal on macOS.

---

## Step 1: Create the Free Tier VM

1.  **Go to GCP Console**: [https://console.cloud.google.com/](https://console.cloud.google.com/)
2.  **Create a New Project**: Call it `vidadu-academy`.
3.  **Go to Compute Engine** -> **VM Instances**.
4.  **Click "Create Instance"**.
5.  **Configure for Free Tier**:
    - **Name**: `vidadu-server`
    - **Region**: Must be `us-west1`, `us-central1`, or `us-east1` (e.g., `us-central1-a`).
    - **Machine Type**: **e2-micro** (2 vCPU, 1 GB memory).
6.  **Boot Disk**:
    - Click **Change**.
    - Operating System: **Ubuntu**.
    - Version: **Ubuntu 22.04 LTS** (x86/64).
    - Boot disk type: **Standard persistent disk**.
    - Size: **30 GB** (Max allowed in free tier).
7.  **Firewall**:
    - Check **Allow HTTP traffic**.
    - Check **Allow HTTPS traffic**.
8.  **Advanced Options -> Networking** (Optional/Reserved IP):
    - Under Network interfaces, click the default interface.
    - External IPv4 address: **Create IP Address** (Reserve a static IP so it doesn't change on reboot). Name it `vidadu-ip`.
9.  **Click Create**.

---

## Step 2: Server Setup

1.  **SSH into the Server**:

    - On the VM Instances list, click **SSH** (opens in browser window) OR setup local SSH access via gcloud CLI (recommended for file transfer, but browser SSH works for commands).
    - _Tip: In the browser SSH window, you can upload/download files using the gear icon._

2.  **Install Docker & Docker Compose**:

    ```bash
    # Update packages
    sudo apt update && sudo apt upgrade -y

    # Install prerequisites
    sudo apt install -y ca-certificates curl gnupg lsb-release git

    # Add Docker GPG key
    sudo mkdir -m 0755 -p /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

    # Add Repo
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

    # Install Docker
    sudo apt update
    sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Add user to Docker group
    sudo usermod -aG docker $USER
    # You might need to close the SSH window and open it again for this to apply!
    ```

3.  **Setup Swap Memory** (Crucial for 1GB RAM!):
    Even with SQLite, 1GB is tight. A swap file prevents crashes.
    ```bash
    sudo fallocate -l 2G /swapfile
    sudo chmod 600 /swapfile
    sudo mkswap /swapfile
    sudo swapon /swapfile
    echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
    ```

---

## Step 3: Application Deployment

1.  **Clone the Repository**:

    ```bash
    git clone https://github.com/YourUsername/VidaduAcademy.git
    cd VidaduAcademy
    ```

2.  **Prepare SQLite Database**:
    We need to create the database file on the host so Docker can mount it.

    ```bash
    # Create the database file
    touch backend/database/database.sqlite
    ```

3.  **Setup Environment Variables**:

    - **Backend**:

      ```bash
      cp backend/.env.example backend/.env
      nano backend/.env
      ```

      **CRITICAL CHANGES**:

      - `APP_URL=https://your-domain.com`
      - `DB_CONNECTION=sqlite`
      - `DB_DATABASE=/var/www/database/database.sqlite` (This matches strict container path)
      - `CACHE_DRIVER=file`
      - `SESSION_DRIVER=file`
      - `QUEUE_CONNECTION=database`
      - Remove `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`.
      - Remove `REDIS_HOST`, etc.

    - **Frontend**:
      - Make sure your `frontend/src` code points to the correct API. If it is hardcoded, change it. If environment based, check `docker-compose` or `frontend/.env`.

4.  **Configure Caddy**:

    ```bash
    nano Caddyfile
    ```

    - Change `your-email@example.com` and `your-domain.com`.

5.  **Start the Application**:

    ```bash
    docker compose -f docker-compose.prod.yml up -d --build
    ```

6.  **Finalize Setup**:

    ```bash
    # Run migrations (creates the tables in your sqlite file)
    docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

    # Link storage
    docker compose -f docker-compose.prod.yml exec app php artisan storage:link

    # Optimize
    docker compose -f docker-compose.prod.yml exec app php artisan optimize
    ```

---

## Step 4: Verify & Maintain

1.  **Check Status**:

    ```bash
    docker compose -f docker-compose.prod.yml ps
    ```

    All containers (`vidadu-app`, `vidadu-web`, `vidadu-caddy`, `vidadu-worker`, `vidadu-scheduler`) should be `Up`.

2.  **Visit**: `https://your-domain.com`.

3.  **Updates**:
    Run the update script (make it executable first: `chmod +x update.sh`):
    ```bash
    ./update.sh
    ```

### Troubleshooting RAM Issues

If the server crashes:

1.  Check memory: `free -h`
2.  If `app` container exits, check logs: `docker logs vidadu-app`.
