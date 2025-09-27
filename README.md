# POS Xpress Backend

A comprehensive backend system providing REST API services for mobile Point of Sale applications with offline-first capabilities and a Filament-based admin panel.

## Features

- **Multi-tier Subscriptions**: Basic, Pro, Enterprise plans with feature gating
- **Multi-outlet Management**: Support for multiple store locations
- **Inventory Tracking**: Real-time stock management with COGS calculation
- **Offline-first API**: Mobile POS support with sync capabilities
- **Role-based Access Control**: Multi-level authentication and authorization
- **Comprehensive Reporting**: Automated monthly reports and analytics
- **Admin Panel**: Filament-based administrative interface

## Technology Stack

- **PHP**: 8.4+
- **Laravel**: 12.x
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis 7
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Admin Panel**: FilamentPHP 3.x
- **Containerization**: Docker & Docker Compose

## Quick Start

### Local Development

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd pos-xpress-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development server**
   ```bash
   php artisan serve
   ```

### Docker Development

1. **Start services**
   ```bash
   docker-compose -f docker-compose.yml -f docker-compose.local.yml up -d
   ```

2. **Setup application**
   ```bash
   docker-compose exec app composer install
   docker-compose exec app cp .env.docker .env
   docker-compose exec app php artisan key:generate
   docker-compose exec app php artisan migrate
   docker-compose exec app php artisan db:seed
   ```

3. **Access the application**
   - API: http://localhost/api/v1
   - Admin Panel: http://localhost/admin

## API Documentation

### Authentication

```bash
# Login
POST /api/v1/auth/login
{
  "email": "admin@posxpress.com",
  "password": "password"
}

# Get user info
GET /api/v1/auth/user
Authorization: Bearer {token}
```

### Health Check

```bash
GET /api/v1/health
```

### API Versioning

All API endpoints are versioned with `/api/v1` prefix. Future versions will use `/api/v2`, etc.

## Default Users

After seeding, the following users are available:

- **System Admin**: admin@posxpress.com / password
- **Store Owner**: aziz@xpress.com / password

## Project Structure

```
app/
├── Http/Controllers/Api/V1/    # Versioned API controllers
├── Models/                     # Eloquent models
├── Services/                   # Business logic services
├── Filament/                   # Admin panel resources
└── Providers/                  # Service providers

docker/                         # Docker configuration
├── nginx/                      # Nginx configuration
├── php/                        # PHP configuration
├── mysql/                      # MySQL configuration
├── redis/                      # Redis configuration
└── supervisor/                 # Queue worker configuration
```

## Development Guidelines

### Code Standards
- Follow PSR-12 coding standards
- Use Laravel Pint for code formatting
- Maintain 80%+ test coverage

### API Standards
- RESTful API design
- Consistent JSON response format
- Proper HTTP status codes
- Bearer token authentication

### Database
- Use migrations for schema changes
- Implement proper indexing
- Follow naming conventions

## Deployment

### Production Environment

1. **Build and deploy**
   ```bash
   docker-compose -f docker-compose.yml up -d
   ```

2. **Run migrations**
   ```bash
   docker-compose exec app php artisan migrate --force
   ```

3. **Optimize application**
   ```bash
   docker-compose exec app php artisan config:cache
   docker-compose exec app php artisan route:cache
   docker-compose exec app php artisan view:cache
   ```

### Environment Variables

Key environment variables for production:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_*`: Database configuration
- `REDIS_*`: Redis configuration
- `MAIL_*`: Email configuration

## Monitoring

- **Health Check**: `/api/v1/health`
- **Logs**: `storage/logs/laravel.log`
- **Queue Monitoring**: Supervisor configuration included

## Security

- HTTPS enforced in production
- Rate limiting on API endpoints
- SQL injection prevention via Eloquent
- XSS protection
- CSRF protection for web routes

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is proprietary software. All rights reserved.

## Support

For support and questions, please contact the development team.