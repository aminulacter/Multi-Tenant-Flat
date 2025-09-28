# Flat Management System

A comprehensive multi-tenant flat management system built with Laravel 12, Vue 3, and Inertia.js. This system manages buildings, flats, tenants, and house owners with role-based access control.

## 🏗️ Architecture Overview

### Tech Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 + TypeScript + Inertia.js
- **UI Framework**: Tailwind CSS 4 + Reka UI components
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Authentication**: Laravel Fortify with 2FA support
- **Authorization**: Role-based permissions (spatie/laravel-permission)
- **API**: Laravel Sanctum for API authentication
- **Build Tool**: Vite with TypeScript support

### Multi-Tenant Implementation

This system implements a **role-based multi-tenant architecture** with the following structure:

#### User Roles
1. **SuperAdmin** (role_id = 1)
   - Full system access
   - Can manage users, buildings, and all data
   - Access to all features

2. **House Owner** (role_id = 2)
   - Manages their own buildings and flats
   - Can create bill categories
   - Manages tenants in their properties
   - Limited to their own data

3. **Tenant** (role_id = 3)
   - Views their assigned flat information
   - Limited read-only access to their data

#### Data Isolation Strategy
- **House Owner Isolation**: Each house owner can only access their own buildings, flats, and tenants
- **Tenant Isolation**: Tenants can only view their assigned flat and related information
- **SuperAdmin Override**: SuperAdmin has access to all data across all tenants

#### Key Relationships
```
User (1) → Role (1)
User (1) → HouseOwner (0..1)
User (1) → Tenant (0..1)
HouseOwner (1) → Building (1..*)
Building (1) → Flat (1..*)
Flat (1) → Tenant (0..1)
HouseOwner (1) → BillCategory (1..*)
```

## 🚀 Setup Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (for development) or MySQL/PostgreSQL (for production)

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd FlatManagement
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # For MySQL/PostgreSQL, update .env with your database credentials
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_DATABASE=flat_management
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```

7. **Start development servers**
   ```bash
   # Option 1: Use the convenient dev script (recommended)
   composer run dev
   
   # Option 2: Start servers individually
   php artisan serve
   npm run dev
   ```

8. **Access the application**
   - Main application: http://localhost:8000
   - Vite dev server: http://localhost:5173

### Default Credentials
After running seeders, you can use these default accounts:
- **SuperAdmin**: Check `database/seeders/UserSeeder.php` for default credentials
- **House Owner**: Created through the application
- **Tenant**: Created through the application

## 🏢 Subdomain Configuration (Optional)

For production multi-tenant setup with subdomains:

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name *.yourdomain.com;
    
    root /path/to/FlatManagement/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Environment Variables
```env
APP_URL=https://yourdomain.com
SESSION_DOMAIN=.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,*.yourdomain.com
```

## 🔧 Optimization & Performance

### Database Optimization

#### Query Optimization
- **Eager Loading**: Use `with()` to prevent N+1 queries
  ```php
  // Good
  $buildings = Building::with(['flats.tenant', 'houseOwner'])->get();
  
  // Avoid
  $buildings = Building::all();
  foreach ($buildings as $building) {
      $building->flats; // N+1 query
  }
  ```

- **Database Indexes**: Key indexes are already defined in migrations
  ```sql
  -- Users table
  CREATE INDEX idx_users_role_id ON users(role_id);
  
  -- Flats table
  CREATE INDEX idx_flats_building_id ON flats(building_id);
  CREATE INDEX idx_flats_tenant_id ON flats(tenant_id);
  CREATE INDEX idx_flats_house_owner_id ON flats(house_owner_id);
  ```

#### Caching Strategy
- **Route Caching**: `php artisan route:cache`
- **Config Caching**: `php artisan config:cache`
- **View Caching**: `php artisan view:cache`
- **Application Caching**: Use Redis for session storage and caching

### Frontend Optimization

#### Vite Configuration
- **Code Splitting**: Automatic with Vite
- **Tree Shaking**: Enabled for unused code elimination
- **Asset Optimization**: Images and CSS are optimized during build

#### Vue.js Best Practices
- **Lazy Loading**: Components are loaded on demand
- **Composition API**: Used throughout for better performance
- **TypeScript**: Full type safety and better IDE support

### API Optimization

#### Laravel Sanctum Configuration
```php
// config/sanctum.php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1')),
'expiration' => env('SANCTUM_EXPIRATION', 60 * 24), // 24 hours
```

#### Rate Limiting
- **API Routes**: Protected with rate limiting
- **Authentication**: Throttled login attempts
- **Password Reset**: Limited to 6 attempts per minute

## 🎯 Design Decisions

### 1. Role-Based Multi-Tenancy
**Decision**: Implemented role-based multi-tenancy instead of database-per-tenant
**Rationale**: 
- Simpler deployment and maintenance
- Shared resources are more cost-effective
- Easier to implement cross-tenant features
- Better for small to medium-scale applications

### 2. Inertia.js for SPA Experience
**Decision**: Used Inertia.js instead of separate API + SPA
**Rationale**:
- Faster development with shared routing
- Better SEO capabilities
- Simpler state management
- Reduced complexity compared to separate API

### 3. SQLite for Development
**Decision**: Default to SQLite for local development
**Rationale**:
- Zero configuration required
- Faster setup for new developers
- Good for testing and development
- Easy to switch to MySQL/PostgreSQL for production

### 4. TypeScript for Frontend
**Decision**: Full TypeScript implementation
**Rationale**:
- Better code quality and maintainability
- Improved IDE support and autocomplete
- Catch errors at compile time
- Better refactoring capabilities

### 5. Laravel Wayfinder for Route Generation
**Decision**: Used Laravel Wayfinder for type-safe route generation
**Rationale**:
- Type-safe route generation
- Better IDE support
- Compile-time route validation
- Consistent route naming

## 🔒 Security Considerations

### Authentication & Authorization
- **Laravel Fortify**: Handles authentication with 2FA support
- **Role-based Access Control**: Implemented through middleware
- **Policy-based Authorization**: Each model has corresponding policies
- **CSRF Protection**: Enabled for all forms
- **XSS Protection**: Input sanitization and output escaping

### Data Protection
- **Mass Assignment Protection**: Using `$fillable` arrays
- **SQL Injection Prevention**: Using Eloquent ORM and prepared statements
- **Password Hashing**: Laravel's built-in password hashing
- **Session Security**: Secure session configuration

## 📊 Database Schema

### Core Tables
- `users` - User accounts with role-based access
- `roles` - User roles (SuperAdmin, House Owner, Tenant)
- `house_owners` - House owner profiles
- `tenants` - Tenant profiles
- `buildings` - Building information
- `flats` - Individual flat units
- `bill_categories` - Bill category definitions
- `bills` - Individual bill records

### Permission Tables (spatie/laravel-permission)
- `permissions` - System permissions
- `roles` - Role definitions
- `model_has_permissions` - Direct user permissions
- `model_has_roles` - User role assignments

## 🧪 Testing

### Running Tests
```bash
# Run all tests
composer run test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: Test complete user workflows
- **Unit Tests**: Test individual components
- **Authentication Tests**: Test login/logout flows
- **Authorization Tests**: Test role-based access

## 🚀 Deployment

### Production Deployment
1. **Environment Setup**
   ```bash
   cp .env.example .env
   # Configure production environment variables
   ```

2. **Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm ci && npm run build
   ```

3. **Database**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=RoleSeeder
   ```

4. **Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Docker Deployment (Optional)
```dockerfile
FROM php:8.2-fpm-alpine
# Add your Docker configuration here
```

## 📝 Development Guidelines

### Code Style
- **PHP**: Follow PSR-12 standards (enforced by Laravel Pint)
- **JavaScript/TypeScript**: ESLint + Prettier configuration
- **Vue**: Composition API with `<script setup>` syntax

### Git Workflow
- **Main Branch**: Production-ready code
- **Feature Branches**: New features and bug fixes
- **Pull Requests**: Required for all changes

### API Documentation
- **Routes**: Documented in route files with `@see` annotations
- **Controllers**: Use PHPDoc for method documentation
- **Models**: Document relationships and attributes

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the code comments and PHPDoc blocks

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Multi-tenant flat management system
- Role-based access control
- Vue 3 + TypeScript frontend
- Laravel 12 backend
- Complete CRUD operations for all entities
- Hosted at http://flat-management.pidinternational.com.bd/
