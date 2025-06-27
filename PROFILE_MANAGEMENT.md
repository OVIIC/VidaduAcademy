# VidaduAcademy Profile Management System

## Overview
A comprehensive user profile management system built with Laravel (backend) and Vue.js (frontend), featuring tabbed navigation, secure authentication, and full CRUD operations for user data.

## Features Implemented

### Backend (Laravel + API)
- ✅ User authentication with Laravel Sanctum
- ✅ Profile management endpoints
- ✅ Password update functionality
- ✅ Notification preferences
- ✅ Certificates/achievements system
- ✅ Data export functionality
- ✅ Account deletion with confirmation
- ✅ Comprehensive validation and error handling
- ✅ Database migrations with profile fields

### Frontend (Vue.js + Composition API)
- ✅ Modern, responsive tabbed interface
- ✅ Real-time form validation
- ✅ Secure API integration
- ✅ User authentication state management
- ✅ Success/error notification system
- ✅ Mobile-responsive design
- ✅ Tailwind CSS styling

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/logout` - User logout

### Profile Management
- `GET /api/user/profile` - Get user profile
- `PUT /api/user/profile` - Update user profile
- `PUT /api/user/password` - Update password
- `GET /api/user/certificates` - Get user certificates
- `GET /api/certificate/{id}/download` - Download certificate
- `GET /api/user/export-data` - Export user data
- `DELETE /api/user/account` - Delete user account

## Database Schema

### Users Table Extensions
- `phone` - User phone number
- `location` - User location
- `bio` - User biography
- `youtube_channel` - YouTube channel URL
- `subscriber_count` - Subscriber count range
- `content_niche` - Content niche category
- `goals` - User goals
- `email_notifications` - Email notification preference
- `course_reminders` - Course reminder preference
- `marketing_emails` - Marketing email preference

## Tech Stack

### Backend
- Laravel 10.x
- Laravel Sanctum (API authentication)
- SQLite database
- Spatie Laravel Permissions
- PHP 8.1+

### Frontend
- Vue.js 3 (Composition API)
- Vite build tool
- Tailwind CSS
- Pinia state management
- Axios for API calls

## Installation & Setup

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --port=8002
```

### Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

## Configuration

### Environment Variables

#### Backend (.env)
```env
APP_URL=http://localhost:8002
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:3001,localhost:3002,localhost:3003
FRONTEND_URL=http://localhost:3003
```

#### Frontend (.env)
```env
VITE_API_URL=http://localhost:8002/api
VITE_APP_NAME=VidaduAcademy
```

## Testing

### Test User Credentials
- Email: `test@example.com`
- Password: `newpassword123` (updated during testing)

### Manual Testing
1. Start both backend and frontend servers
2. Navigate to http://localhost:3003
3. Login with test credentials
4. Test all profile management features

### API Testing
Run the included test script:
```bash
./test-profile-system.sh
```

## Profile Management Tabs

### 1. Personal Information
- Basic user details (name, email, phone, location)
- Biography and social media links
- YouTube channel information
- Content niche and goals selection

### 2. Security Settings
- Password change functionality
- Two-factor authentication (future enhancement)
- Login history (future enhancement)

### 3. Notification Preferences
- Email notifications toggle
- Course reminders toggle
- Marketing emails toggle

### 4. Certificates & Achievements
- View completed course certificates
- Download certificate functionality
- Progress tracking

### 5. Account Management
- Data export functionality
- Account deletion with confirmation
- Privacy settings

## Security Features

- ✅ CSRF protection
- ✅ Input validation and sanitization
- ✅ Password hashing (bcrypt)
- ✅ API rate limiting
- ✅ Sanctum token authentication
- ✅ XSS protection
- ✅ SQL injection prevention

## Validation Rules

### Profile Update
- Name: required, string, max:255
- Email: required, email, unique
- Phone: nullable, string, max:20
- Location: nullable, string, max:255
- Bio: nullable, string, max:1000
- YouTube Channel: nullable, URL
- Subscriber Count: predefined options
- Content Niche: predefined categories
- Goals: predefined options

### Password Update
- Current Password: required
- New Password: required, min:8, confirmed
- Password Confirmation: required, must match

## Future Enhancements

- [ ] Profile image upload
- [ ] Two-factor authentication
- [ ] Social media integration
- [ ] Advanced privacy controls
- [ ] Login history tracking
- [ ] Account recovery options
- [ ] Bulk data operations
- [ ] Real-time notifications

## Architecture

```
Frontend (Vue.js)
    ↓ HTTP/HTTPS
API Layer (Laravel Routes)
    ↓
Controllers (Validation & Logic)
    ↓ 
Models (Eloquent ORM)
    ↓
Database (SQLite/MySQL)
```

## Error Handling

- Comprehensive validation errors
- User-friendly error messages
- Graceful API error handling
- Fallback mechanisms
- Success/failure notifications

## Performance Considerations

- Lazy loading of profile data
- Optimized database queries
- Efficient state management
- Minimal API calls
- Responsive UI updates

## Deployment Notes

- Configure CORS for production
- Update SANCTUM_STATEFUL_DOMAINS
- Set up proper SSL certificates
- Configure production database
- Enable Laravel optimizations
- Build frontend for production

## Contributing

1. Follow Laravel and Vue.js best practices
2. Maintain comprehensive test coverage
3. Document all API changes
4. Use consistent coding standards
5. Update this documentation

## License

This project is part of VidaduAcademy platform.
