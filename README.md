# Full Stack Notes Application

A modern note-taking application built with Symfony 6, Vue.js, Python Flask API, and PostgreSQL, all containerized with Docker.

## System Requirements

- Docker Engine 24.0+
- Docker Compose 2.0+
- Git

## Project Structure

```
cocus-symfony/
├── API/                    # Python Flask API
├── assets/                 # Vue.js frontend
├── src/                    # Symfony backend
├── docker/                 # Docker configuration files
├── docker-compose.yml      # Docker services configuration
└── ...
```

## Quick Start

1. Clone the repository:
   ```bash
   git clone https://github.com/cmatosbc/cocus-test.git
   cd cocus-test
   ```

2. Build and start the containers:
   ```bash
   docker-compose up --build -d
   ```

3. Install PHP dependencies:
   ```bash
   docker-compose exec php composer install
   ```

4. Run database migrations:
   ```bash
   docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction
   ```

5. Install frontend dependencies and build assets:
   ```bash
   docker-compose exec php yarn install
   docker-compose exec php yarn build
   ```

## Accessing the Applications

- **Frontend (Symfony + Vue)**: http://localhost:8080
- **Python API**: http://localhost:5000
- **PostgreSQL Database**: localhost:5432 (internal access via `database` hostname)

## Service Details

### Symfony Application (PHP)
- Framework: Symfony 6
- Frontend: Vue.js
- Port: 8080
- Environment variables in `.env`

### Python API
- Framework: Flask
- Port: 5000
- Environment variables in `API/.env`

### PostgreSQL Database
- Version: 16
- Default credentials:
  - Database: app
  - Username: postgres
  - Password: postgres
- Port: 5432

## Docker Services

1. **php**: PHP-FPM service running Symfony
2. **nginx**: Web server for Symfony application
3. **database**: PostgreSQL database
4. **python_api**: Flask API service

## Development

### File Permissions
The application uses proper file permissions with:
- Non-root users in containers
- Proper volume mounts
- Write permissions for cache and logs

### Hot Reloading
- Symfony changes are immediately reflected
- Vue.js components hot reload automatically
- Python API has auto-reload enabled

### Database Management
- Access PostgreSQL:
  ```bash
  docker-compose exec database psql -U postgres -d app
  ```
- Create new migrations:
  ```bash
  docker-compose exec php bin/console doctrine:migrations:diff
  ```

## Troubleshooting

1. **Permission Issues**
   ```bash
   docker-compose exec php chown -R www-data:www-data var/
   ```

2. **Database Connection Issues**
   - Check if database is running:
     ```bash
     docker-compose ps database
     ```
   - Verify credentials in `.env` and `API/.env`

3. **Cache Issues**
   ```bash
   docker-compose exec php bin/console cache:clear
   ```

## API Documentation

### Symfony Endpoints
- `POST /api/register`: User registration
- `POST /api/login`: User authentication
- `GET /api/notes`: List user's notes
- `POST /api/notes`: Create new note
- `PUT /api/notes/{id}`: Update note
- `DELETE /api/notes/{id}`: Delete note

### Python API Endpoints
- `GET /health`: API health check
- Additional endpoints documented in `API/app.py`

## Security

- CORS properly configured
- PostgreSQL credentials secured
- Non-root users in containers
- Proper file permissions
- Environment-specific configurations

## License

This project is licensed under the MIT License.
