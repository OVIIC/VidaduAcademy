# VidaduAcademy - Local-First LMS

A comprehensive Learning Management System focused on YouTube growth and channel management, combining modern web technologies for creators and marketers.

## 🔧 Tech Stack

- **Frontend**: Vue.js 3 with Vite
- **Backend**: Laravel 10 with Filament Admin Panel
- **Database**: MySQL (via Laravel Sail)
- **Authentication**: Laravel Sanctum
- **Payments**: Stripe Checkout + Webhooks
- **Local Development**: Dockerized using Laravel Sail
- **Deployment**: Railway (optional)

## 🧩 Key Features

- **Course Management**: Create, organize, and manage video courses
- **Stripe Integration**: Secure payment processing with automatic course assignment
- **User Authentication**: Registration, login, and role-based access control
- **Admin Panel**: Filament-powered admin interface for content management
- **Local Media Storage**: Upload and manage course videos locally
- **Offline Development**: Fully functional without internet connectivity
- **YouTube Focus**: Specialized tools for YouTube growth and channel management

## 📁 Project Structure

```
VidaduAcademy/
├── backend/                    # Laravel API + Admin Panel
│   ├── app/
│   │   ├── Models/            # Course, User, Payment models
│   │   ├── Http/Controllers/  # API controllers
│   │   ├── Filament/          # Admin panel resources
│   │   └── Services/          # Business logic (Stripe, etc.)
│   ├── database/
│   │   ├── migrations/        # Database schema
│   │   └── seeders/           # Sample data
│   ├── storage/               # Local file storage
│   └── docker-compose.yml     # Laravel Sail configuration
├── frontend/                  # Vue.js SPA
│   ├── src/
│   │   ├── components/        # Reusable Vue components
│   │   ├── views/             # Page components
│   │   ├── stores/            # Pinia state management
│   │   └── services/          # API communication
│   ├── public/                # Static assets
│   └── vite.config.js         # Vite configuration
└── docs/                      # Documentation
```

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- Node.js 18+ & npm
- Git

### Local Development

#### Backend Setup

```bash
cd backend
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan filament:make-user
```

#### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

### Railway Deployment

For production deployment on Railway:

```bash
# Quick setup (requires Railway CLI)
./railway-quick-setup.sh

# Manual setup
npm install -g @railway/cli
railway login
# Follow the deployment guide
```

See [RAILWAY_DEPLOYMENT_COMPLETE.md](RAILWAY_DEPLOYMENT_COMPLETE.md) for detailed deployment instructions.

### Render Deployment (Recommended)

For free production deployment on Render:

```bash
# Quick setup
./render-setup.sh

# Manual setup - Go to render.com
# Follow the deployment guide
```

See [RENDER_DEPLOYMENT_GUIDE.md](RENDER_DEPLOYMENT_GUIDE.md) for detailed deployment instructions.

### Environment Variables

#### Local Development
Copy `.env.example` to `.env` in the backend directory and configure:

```env
# Database (Sail handles this automatically)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=vidadu_academy
DB_USERNAME=sail
DB_PASSWORD=password

# Stripe Configuration
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Laravel Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000

# File Storage
FILESYSTEM_DISK=local
```

#### Production (Railway)
See [railway-env-template.md](railway-env-template.md) for Railway environment variables.

#### Production (Render)  
See [render-env-template.md](render-env-template.md) for Render environment variables.

## 📚 Documentation

- [Installation Guide](docs/installation.md)
- [API Documentation](docs/api.md)
- [Admin Panel Guide](docs/admin.md)
- [Stripe Integration](docs/payments.md)
- [Local Development](docs/deployment.md)
- **[Railway Deployment Guide](RAILWAY_DEPLOYMENT_COMPLETE.md)** 🚂
- **[Render Deployment Guide](RENDER_DEPLOYMENT_GUIDE.md)** 🎨 ⭐
- [Railway Environment Variables](railway-env-template.md)
- [Render Environment Variables](render-env-template.md)

## 🛠️ Deployment Tools

- `render-setup.sh` - **Automated Render deployment preparation** ⭐
- `railway-quick-setup.sh` - Automated Railway deployment
- `railway-monitor.sh` - Monitor Railway deployment status
- `railway-network-test.sh` - Test network connectivity
- `setup.sh` - Local development setup

## 🔒 Security Features

- CSRF Protection
- SQL Injection Prevention
- XSS Protection
- Rate Limiting
- Secure File Upload
- Payment Webhook Verification

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
