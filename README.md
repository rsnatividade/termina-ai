<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Termina AI

A Laravel-based application that integrates with Evolution API for WhatsApp automation and message handling.

## Tech Stack

### Backend
- PHP 8.x
- Laravel 10.x
- Laravel Sanctum (Authentication)
- MySQL/PostgreSQL

### APIs Integration
- Evolution API (WhatsApp Integration)
  - Base URL: https://wp.chatltv.com.br

### Dependência de projetos
Esse projeto de API é utilizado pelos projetos front-end
- https://github.com/AllanOliveiraM/termina-ai-rooms-back
- https://github.com/camilaffonseca/termina-ai-front

## API Documentation

### Termination Process

#### Start Termination
```http
POST /api/start-termination
```
Initiates the termination process workflow.

### Token Management

#### Get Token Info
```http
GET /api/token/{token}
```
Retrieve information about a specific token.

### WhatsApp Integration (Evolution API)

#### Webhook Handler
```http
POST /api/webhook/evolution
```
Handles incoming webhooks from Evolution API.

### Health Check

#### API Status
```http
GET /api/health
```
Check API health status and timestamp.

## Application Flow

1. **Authentication Flow**
   - User registers or logs in
   - Receives authentication token
   - Uses token for subsequent requests

2. **Termination Process Flow**
   - Authenticated user initiates termination process
   - System creates WhatsApp group via Evolution API
   - Adds participants to the group
   - Updates group information and image
   - Sends initial messages

3. **Webhook Processing**
   - Evolution API sends webhooks for WhatsApp events
   - System processes incoming messages
   - Updates relevant records
   - Triggers appropriate responses

## Environment Configuration

Required environment variables:
```env
EVOLUTION_API_URL=https://wp.chatltv.com.br
EVOLUTION_API_KEY=your-api-key
EVOLUTION_API_INSTANCE=chatltv
EVOLUTION_API_TIMEOUT=30
EVOLUTION_API_RETRY_ATTEMPTS=3
EVOLUTION_API_RETRY_DELAY=5
```

## Security

- API authentication using Laravel Sanctum
- Protected routes requiring valid tokens
- Secure webhook handling
- Environment-based configuration
- No hardcoded credentials

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Configure database in `.env`
6. Run migrations:
   ```bash
   php artisan migrate
   ```
7. Configure Evolution API credentials in `.env`

## Development

To start the development server:
```bash
php artisan serve
```

## Testing

Run the test suite:
```bash
php artisan test
```

## Error Handling

The API returns standard HTTP status codes:
- 200: Success
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 422: Validation Error
- 500: Server Error

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## License

This project is licensed under the MIT License.
