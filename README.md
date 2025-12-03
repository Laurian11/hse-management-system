# HSE Management System

A comprehensive Health, Safety, and Environment (HSE) Management System built with Laravel 12, designed for multi-tenant organizations to manage safety compliance, incidents, toolbox talks, and safety communications.

## Features

### 🎯 Core Modules

- **Incident Management**: Report, track, and manage safety incidents with image attachments
- **Toolbox Talks**: Interactive 15-minute safety briefings with biometric attendance
- **Safety Communications**: Multi-channel safety messaging (Email, SMS, Digital Signage, Mobile Push)
- **User Management**: Multi-tenant user management with role-based access control
- **Activity Logging**: Comprehensive audit trail of all system activities
- **Biometric Integration**: ZKTeco K40 fingerprint device integration for attendance

### 🏗️ Architecture

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Tailwind CSS 4.0, Vite
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Design System**: Centralized UI configuration with reusable components

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- SQLite (development) or MySQL/PostgreSQL (production)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd hse-management-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure environment variables**
   Edit `.env` file with your database and application settings:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   
   # ZKTeco K40 Configuration
   ZKTECO_DEVICE_IP=192.168.1.201
   ZKTECO_PORT=4370
   ZKTECO_API_KEY=your_api_key
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start development server**
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
composer pint
```

### Development Mode
```bash
npm run dev
```

This will start Vite in watch mode for hot module replacement.

## Project Structure

```
app/
├── Http/
│   ├── Controllers/      # Application controllers
│   └── Requests/         # Form request validation
├── Models/               # Eloquent models
├── Services/             # Business logic services
├── Exceptions/          # Custom exceptions
└── View/Components/      # Blade components

database/
├── migrations/          # Database migrations
├── factories/           # Model factories
└── seeders/            # Database seeders

resources/
├── views/               # Blade templates
├── css/                # Stylesheets
└── js/                 # JavaScript files

config/
└── ui_design.php       # Design system configuration
```

## Key Modules

### Incident Management
- Report incidents with images
- Track incident status (reported, investigating, closed)
- Assign incidents to users
- Severity classification (low, medium, high, critical)
- Reference number generation

### Toolbox Talks
- Schedule and conduct safety talks
- Biometric attendance tracking
- GPS location verification
- Feedback collection
- Recurring talks support
- Topic library management

### Safety Communications
- Multi-channel delivery (Email, SMS, Digital Signage, Mobile Push)
- Targeted audience selection
- Acknowledgment tracking
- Priority-based messaging

## API Documentation

See [API_DOCUMENTATION.md](API_DOCUMENTATION.md) for detailed API endpoint documentation.

## Design System

The application uses a centralized design system. See [DESIGN_SYSTEM.md](DESIGN_SYSTEM.md) for details on:
- Color schemes
- Typography
- Components
- Custom Blade directives

## Toolbox Talk Module

Comprehensive documentation available in [TOOLBOX_TALK_MODULE.md](TOOLBOX_TALK_MODULE.md)

## Configuration

### ZKTeco K40 Integration

Configure the biometric device in `.env`:
```env
ZKTECO_DEVICE_IP=192.168.1.201
ZKTECO_PORT=4370
ZKTECO_API_KEY=your_api_key
ZKTECO_TIMEOUT=10
ZKTECO_RETRY_ATTEMPTS=3
```

### Database

For production, update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hse_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Security

- Password hashing (bcrypt)
- CSRF protection
- Role-based access control (RBAC)
- Multi-tenant data isolation
- Activity logging
- Session management
- Account locking mechanism

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test files:
```bash
php artisan test --filter IncidentTest
```

## Contributing

1. Create a feature branch
2. Make your changes
3. Write/update tests
4. Ensure all tests pass
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions:
- Check the documentation files
- Review the API documentation
- Open an issue on the repository

## Changelog

### Version 1.0.0 (December 2025)
- Initial release
- Incident management module
- Toolbox talk module
- Safety communications module
- User management with RBAC
- ZKTeco K40 integration
- Design system implementation

---

**Built with ❤️ using Laravel**
