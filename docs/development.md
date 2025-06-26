# Development Guide

## Quick Start

### Prerequisites
- Docker Desktop
- Node.js 18+
- Git

### Setup
```bash
# Clone and setup
git clone <repository-url>
cd VidaduAcademy
chmod +x setup.sh
./setup.sh
```

### Daily Development

```bash
# Start backend (if not running)
cd backend
./vendor/bin/sail up -d

# Start frontend
cd frontend
npm run dev
```

Access points:
- Frontend: http://localhost:3000
- Backend API: http://localhost:8000
- Admin Panel: http://localhost:8000/admin

## Project Structure

```
VidaduAcademy/
├── backend/                 # Laravel 10 + Filament
│   ├── app/
│   │   ├── Models/         # Eloquent models
│   │   ├── Http/Controllers/Api/  # API controllers
│   │   ├── Filament/       # Admin panel resources
│   │   └── Services/       # Business logic
│   ├── database/
│   │   ├── migrations/     # Database schema
│   │   └── seeders/        # Sample data
│   └── routes/api.php      # API routes
├── frontend/               # Vue.js 3 + Vite
│   ├── src/
│   │   ├── components/     # Reusable components
│   │   ├── views/          # Page components
│   │   ├── stores/         # Pinia state management
│   │   └── services/       # API communication
│   └── public/             # Static assets
└── docs/                   # Documentation
```

## Key Features

### Backend (Laravel + Filament)
- **Authentication**: Laravel Sanctum for SPA auth
- **Admin Panel**: Filament for content management
- **API**: RESTful API for frontend consumption
- **Payments**: Stripe integration with webhooks
- **Database**: MySQL with comprehensive schema
- **File Storage**: Local storage for course media

### Frontend (Vue.js)
- **Framework**: Vue 3 with Composition API
- **Build Tool**: Vite for fast development
- **Styling**: Tailwind CSS with custom components
- **State Management**: Pinia stores
- **Routing**: Vue Router with guards
- **UI Components**: Headless UI + custom components

## Development Workflow

### Backend Development

```bash
cd backend

# Run migrations
./vendor/bin/sail artisan migrate

# Create new migration
./vendor/bin/sail artisan make:migration create_table_name

# Create new model
./vendor/bin/sail artisan make:model ModelName -m

# Create API controller
./vendor/bin/sail artisan make:controller Api/ControllerName

# Create Filament resource
./vendor/bin/sail artisan make:filament-resource ResourceName

# Run seeders
./vendor/bin/sail artisan db:seed

# Clear caches
./vendor/bin/sail artisan optimize:clear
```

### Frontend Development

```bash
cd frontend

# Install dependencies
npm install

# Start dev server
npm run dev

# Build for production
npm run build

# Lint code
npm run lint

# Format code
npm run format
```

## Database Schema

### Core Tables
- `users` - User accounts and profiles
- `categories` - Course categories
- `courses` - Course information
- `lessons` - Individual lesson content
- `enrollments` - User course enrollments
- `purchases` - Payment records
- `lesson_progress` - Learning progress tracking

### Key Relationships
- Users can have many enrollments and purchases
- Courses belong to categories and instructors (users)
- Lessons belong to courses
- Progress tracking links users, lessons, and enrollments

## API Endpoints

### Public Endpoints
```
GET /api/courses - List all courses
GET /api/courses/featured - Featured courses
GET /api/courses/categories - Course categories
GET /api/courses/{slug} - Course details
```

### Authenticated Endpoints
```
GET /api/user - Current user info
POST /api/payments/checkout - Create Stripe session
GET /api/learning/courses - User's enrolled courses
GET /api/learning/course/{slug} - Course content
POST /api/learning/course/{slug}/lesson/{slug}/progress - Update progress
```

## Environment Configuration

### Backend (.env)
```env
# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=vidadu_academy
DB_USERNAME=sail
DB_PASSWORD=password

# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Laravel Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
```

### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api
VITE_APP_NAME=VidaduAcademy
```

## Testing

### Backend Testing
```bash
cd backend
./vendor/bin/sail test
./vendor/bin/sail test --coverage
```

### Frontend Testing
```bash
cd frontend
npm run test
npm run test:coverage
```

## Deployment

### Production Build
```bash
# Backend
cd backend
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
cd frontend
npm run build
```

### Railway Deployment
The project is configured for Railway deployment with automatic Docker builds.

## Troubleshooting

### Common Issues

1. **Docker containers not starting**
   ```bash
   cd backend
   ./vendor/bin/sail down
   ./vendor/bin/sail up -d --build
   ```

2. **Database connection issues**
   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```

3. **Frontend API errors**
   - Check CORS configuration in `config/cors.php`
   - Verify API URL in frontend `.env`

4. **Permission issues**
   ```bash
   cd backend
   ./vendor/bin/sail artisan storage:link
   sudo chown -R $USER:$USER storage bootstrap/cache
   ```

## Best Practices

### Code Style
- Use PSR-12 for PHP code
- Follow Vue.js style guide
- Use Prettier for frontend formatting
- Use Pint for Laravel formatting

### Security
- Always validate input data
- Use Laravel's built-in security features
- Implement proper CORS policies
- Validate Stripe webhooks

### Performance
- Use Laravel caching for heavy queries
- Implement Vue component lazy loading
- Optimize images for web
- Use CDN for static assets in production

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Vue.js Guide](https://vuejs.org/guide/)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Stripe API](https://stripe.com/docs/api)
