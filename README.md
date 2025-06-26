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

### Backend Setup

```bash
cd backend
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan filament:make-user
```

### Frontend Setup

```bash
cd frontend
npm install
npm run dev
```

### Environment Variables

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

## 📚 Documentation

- [Installation Guide](docs/installation.md)
- [API Documentation](docs/api.md)
- [Admin Panel Guide](docs/admin.md)
- [Stripe Integration](docs/payments.md)
- [Deployment Guide](docs/deployment.md)

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
