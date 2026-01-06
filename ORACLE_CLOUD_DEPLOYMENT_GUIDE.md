# Oracle Cloud Free Tier Deployment Guide

This guide will walk you through deploying the **VidaduAcademy** application to an Oracle Cloud Infrastructure (OCI) Always Free instance.

## Prerequisites

1.  **Oracle Cloud Account**: An active OCI account.
2.  **Domain Name**: A domain you own (e.g., `vidadu.sk`), pointed to Oracle Cloud nameservers or with A-records ready to point to your instance IP.
3.  **SSH Client**: Terminal on macOS/Linux.

---

## Step 1: Create an Oracle Cloud Instance

We will use the **Ampere (ARM)** instance, which is powerful and always free (up to 4 OCPUs, 24GB RAM).

1.  **Login to OCI Console**.
2.  Go to **Compute** -> **Instances**.
3.  Click **Create Instance**.
4.  **Name**: `vidadu-server` (or similar).
5.  **Placement**: Default is fine.
6.  **Image and Shape**:
    - Click **Edit**.
    - **Image**: Select **Canonical Ubuntu** (22.04 or 24.04).
    - **Shape**: Select **Ampere** -> **VM.Standard.A1.Flex**.
    - **OCPUs**: Select 2 or 4.
    - **Memory**: Select 12GB or 24GB.
7.  **Networking**:
    - Create a new VCN (Virtual Cloud Network) if you don't have one.
    - Ensure "Assign a public IPv4 address" is `Yes`.
8.  **Add SSH Keys**:
    - Select **Upload public key files**.
    - Upload your `id_rsa.pub` (from `~/.ssh/id_rsa.pub` on your Mac). Use `cat ~/.ssh/id_rsa.pub | pbcopy` to copy it.
9.  Click **Create**.

---

## Step 2: Configure Networking (Open Ports)

1.  Click on the **VCN** link (e.g., `vidadu-server-vcn`).
2.  Click on **Security Lists**.
3.  Click on the **Default Security List**.
4.  **Add Ingress Rules**:
    - **Source CIDR**: `0.0.0.0/0`
    - **IP Protocol**: TCP
    - **Destination Port Range**: `80,443`
    - **Description**: HTTP and HTTPS
5.  Click **Add Ingress Rules**.

---

## Step 3: Server Setup

1.  **Connect to your instance**:

    ```bash
    ssh ubuntu@<YOUR_SERVER_IP>
    ```

2.  **Open firewall on the server itself** (Ubuntu uses iptables/netfilter, Oracle images have generous defaults but let's be sure):

    ```bash
    sudo iptables -I INPUT 6 -m state --state NEW -p tcp --dport 80 -j ACCEPT
    sudo iptables -I INPUT 6 -m state --state NEW -p tcp --dport 443 -j ACCEPT
    sudo netfilter-persistent save
    ```

3.  **Install Docker & Docker Compose**:

    ```bash
    # Update packages
    sudo apt update && sudo apt upgrade -y

    # Install prerequisites
    sudo apt install -y ca-certificates curl gnupg lsb-release git

    # Add Docker's official GPG key
    sudo mkdir -m 0755 -p /etc/apt/keyrings
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

    # Set up the repository
    echo \
      "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
      $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

    # Install Docker Engine
    sudo apt update
    sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

    # Add user to docker group (avoid using sudo for docker commands)
    sudo usermod -aG docker $USER
    ```

    **Logout and login again** for the group change to take effect.

---

## Step 4: Application Deployment

1.  **Clone the Repository**:

    ```bash
    git clone https://github.com/YourUsername/VidaduAcademy.git
    cd VidaduAcademy
    ```

2.  **Setup Environment Variables**:

    - Backend:

      ```bash
      cp backend/.env.example backend/.env
      nano backend/.env
      ```

      Edit the following:

      - `APP_URL=https://your-domain.com`
      - `DB_HOST=db`
      - `DB_PASSWORD=secret_db_password` (Make this strong!)
      - `REDIS_HOST=redis`
      - Configure Mail settings (SMTP) for emails to work.

    - Frontend (if needed):
      - Usually frontend builds use build-time variables. Ensure your `.env` or build process reflects the production API URL.
      - Check `frontend/.env` if you have one. `VITE_API_URL` should be `https://your-domain.com`.

3.  **Configure Caddy**:

    - Edit `Caddyfile`:
      ```bash
      nano Caddyfile
      ```
    - Replace `your-email@example.com` with your real email.
    - Replace `your-domain.com` with your actual domain name.

4.  **Configure Docker Compose**:

    - Edit `docker-compose.prod.yml`:
      ```bash
      nano docker-compose.prod.yml
      ```
    - Ensure `MYSQL_ROOT_PASSWORD` and `MYSQL_PASSWORD` match what you put in `backend/.env`.

5.  **Make Update Script Executable**:

    ```bash
    chmod +x update.sh
    ```

6.  **Start the Application**:

    ```bash
    docker compose -f docker-compose.prod.yml up -d --build
    ```

7.  **Finalize Setup**:

    ```bash
    # Run migrations
    docker compose -f docker-compose.prod.yml exec app php artisan migrate --force

    # Link storage
    docker compose -f docker-compose.prod.yml exec app php artisan storage:link

    # Optimize
    docker compose -f docker-compose.prod.yml exec app php artisan optimize
    ```

---

## Step 5: Verify Deployment

1.  Visit `https://your-domain.com` in your browser.
2.  Caddy should automatically obtain an SSL certificate.
3.  Test login, registration, and file uploads.

---

## Maintenance and Updates

To update your application later (after you push changes to GitHub), simply run:

```bash
cd ~/VidaduAcademy
./update.sh
```

This script will:

1.  Pull the latest code.
2.  Rebuild changed containers.
3.  Restart services.
4.  Clear caches.
