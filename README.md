# Cocus Symfony Project

A Symfony 6 project with Vue.js frontend, JWT authentication, and PostgreSQL database.

## Prerequisites

- Docker and Docker Compose (recommended)
- Node.js (v18 or higher)
- PHP (v8.2 or higher) - only needed for local development without Docker
- Composer - only needed for local development without Docker

## Using Docker (Recommended)

### Initial Setup

1. Clone the repository:
```bash
git clone https://github.com/cmatosbc/cocus-test.git
cd cocus-test
```

2. Copy the environment files:
```bash
cp .env.example .env
cp .env.test.example .env.test
```

3. Build and start the containers:
```bash
docker-compose up -d --build
```

This will:
- Build the PHP container with all dependencies
- Start PostgreSQL database
- Start Nginx web server
- Start Node.js for asset compilation

### Accessing the Application

- Web interface: http://localhost:8080
- Database: localhost:5432 (username: app, password: app)

### Development Commands

- Rebuild assets and restart containers:
```bash
npm run docker-build
```

- Create database dump:
```bash
npm run docker-dbdump
```

- Access the PHP container:
```bash
docker-compose exec php bash
```

- Access the database container:
```bash
docker-compose exec database psql -U app app
```

## Local Development (Without Docker)

### Initial Setup

1. Clone the repository:
```bash
git clone https://github.com/cmatosbc/cocus-test.git
cd cocus-test
```

2. Copy the environment files:
```bash
cp .env.example .env
cp .env.test.example .env.test
```

3. Install PHP dependencies:
```bash
composer install
```

4. Install Node.js dependencies:
```bash
npm install
```

5. Build the assets:
```bash
npm run build
```

### Development Server

Start the development server:
```bash
npm run dev-server
```

The application will be available at http://localhost:8080

### Database Setup

1. Create the database:
```bash
php bin/console doctrine:database:create
```

2. Run migrations:
```bash
php bin/console doctrine:migrations:migrate
```

3. Load fixtures (if any):
```bash
php bin/console doctrine:fixtures:load
```

## Directory Structure

```
cocus-symfony/
├── assets/              # Frontend assets (Vue.js, CSS, JS)
├── config/              # Symfony configuration
├── db/                  # Database dumps
├── public/              # Web root
├── src/                 # PHP source code
├── templates/           # Twig templates
└── tests/               # Tests
```

## Environment Variables

The project uses several environment files:

- `.env`: Main environment file
- `.env.test`: Test environment file
- `.env.local`: Local environment overrides (gitignored)

## Security

The project uses JWT authentication. Make sure to:

1. Generate JWT keys:
```bash
php bin/console lexik:jwt:generate-keypair
```

2. Configure the keys in your `.env` file:
```
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE='your-secret-passphrase'
```

## Running Tests

```bash
php bin/phpunit
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the UNLICENSED license - see the LICENSE file for details.
