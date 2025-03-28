# Cocus Symfony Project

A modern web application built with:
- Symfony 6 (PHP Backend)
- Vue.js (JavaScript Frontend)
- Python Flask API
- PostgreSQL Database
- JWT Authentication

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [First Time Setup](#first-time-setup)
3. [Daily Development](#daily-development)
4. [Working with Individual Components](#working-with-individual-components)
5. [Troubleshooting](#troubleshooting)

## Prerequisites

You only need to install these tools once on your computer:

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) - Run the application in containers
- [Git](https://git-scm.com/downloads) - Download and manage the code

For Windows users:
- Use WSL2 (Windows Subsystem for Linux) for better performance
- Install Docker Desktop with WSL2 backend

## First Time Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/cmatosbc/cocus-test.git
   cd cocus-test
   ```

2. Copy environment files:
   ```bash
   cp .env.example .env
   cp API/.env.example API/.env
   ```

3. Build and start all containers:
   ```bash
   docker-compose up -d --build
   ```

4. Install frontend dependencies:
   ```bash
   npm install
   ```

5. Build frontend assets:
   ```bash
   npm run docker-build
   ```

The application should now be running at:
- Frontend: http://localhost:8080
- Symfony API: http://localhost:8080/api
- Python API: http://localhost:5000/python_api

## Daily Development

### Starting the Application
```bash
docker-compose up -d
```

### Stopping the Application
```bash
docker-compose down
```

### Viewing Logs
```bash
# All containers
docker-compose logs -f

# Specific container
docker-compose logs -f php    # PHP/Symfony logs
docker-compose logs -f nginx  # Web server logs
docker-compose logs -f node   # Frontend build logs
docker-compose logs -f python_api  # Python API logs
```

## Working with Individual Components

### Frontend (Vue.js)
1. Make changes to files in `assets/` directory
2. Rebuild frontend:
   ```bash
   npm run docker-build
   ```

### Symfony Backend
1. Install new dependencies:
   ```bash
   docker-compose exec php composer require package-name
   ```

2. Create database migrations:
   ```bash
   docker-compose exec php bin/console make:migration
   docker-compose exec php bin/console doctrine:migrations:migrate
   ```

3. Clear cache:
   ```bash
   docker-compose exec php bin/console cache:clear
   ```

### Python API
1. Install new Python packages:
   ```bash
   # Add package to API/requirements.txt
   docker-compose up -d --build python_api
   ```

2. Restart after code changes:
   ```bash
   docker-compose restart python_api
   ```

### Database
1. Access PostgreSQL:
   ```bash
   docker-compose exec database psql -U app -d app
   ```

2. Reset Database:
   ```bash
   docker-compose down
   docker volume rm cocus-symfony_database_data
   docker-compose up -d
   ```

## Troubleshooting

### Container Issues
1. If containers aren't starting:
   ```bash
   # Stop all containers
   docker-compose down

   # Remove all containers and volumes
   docker-compose down -v

   # Rebuild everything
   docker-compose up -d --build
   ```

2. If frontend changes aren't showing:
   ```bash
   npm run docker-build
   ```

3. If Symfony gives permission errors:
   ```bash
   docker-compose exec php chown -R www-data:www-data var/
   ```

### Common Error Messages

1. "Connection refused" to database:
   - Wait 30 seconds after starting containers
   - Database might still be initializing

2. "No such file or directory" in Symfony:
   ```bash
   docker-compose exec php composer install
   docker-compose exec php bin/console cache:clear
   ```

3. "Invalid date format" in notes:
   - Clear browser cache
   - Rebuild frontend with `npm run docker-build`

### Getting Help
If you encounter any issues not covered here:
1. Check the container logs (see "Viewing Logs" section)
2. Ensure all containers are running: `docker-compose ps`
3. Contact the development team

## License

This project is proprietary and confidential. 2025 Cocus
