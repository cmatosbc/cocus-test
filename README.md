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
   docker-compose exec php npm install
   docker-compose exec php npm run build
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

## Using the Application

### User Authentication

1. **Registration**:
   - Visit http://localhost:8080/register
   - Fill in the registration form with:
     - Name
     - Email
     - Password
   - Submit the form to create your account

2. **Login**:
   - Visit http://localhost:8080/login
   - Enter your email and password
   - Upon successful login, you'll receive a session cookie
   - You'll be redirected to the dashboard

### Dashboard

The dashboard at http://localhost:8080/dashboard provides access to:
- Note management (create, read, update, delete)
- User profile information
- Logout functionality

### API Access

#### Symfony API Endpoints (Authentication Required)

All requests require a valid session cookie obtained after login.

1. **Notes Management**:
   ```bash
   # List all notes
   curl -X GET http://localhost:8080/api/notes -H "Cookie: <your-session-cookie>"

   # Create a note
   curl -X POST http://localhost:8080/api/notes \
     -H "Cookie: <your-session-cookie>" \
     -H "Content-Type: application/json" \
     -d '{"title":"My Note","message":"Content","type":0}'

   # Update a note
   curl -X PUT http://localhost:8080/api/notes/{id} \
     -H "Cookie: <your-session-cookie>" \
     -H "Content-Type: application/json" \
     -d '{"title":"Updated Note","message":"New Content","type":1}'

   # Delete a note
   curl -X DELETE http://localhost:8080/api/notes/{id} \
     -H "Cookie: <your-session-cookie>"
   ```

2. **Authentication**:
   ```bash
   # Register
   curl -X POST http://localhost:8080/api/register \
     -H "Content-Type: application/json" \
     -d '{"name":"User","email":"user@example.com","password":"password123"}'

   # Login
   curl -X POST http://localhost:8080/api/login \
     -H "Content-Type: application/json" \
     -d '{"email":"user@example.com","password":"password123"}'
   ```

#### Python API Endpoints

The Python API provides additional functionality:
- Health check: `GET http://localhost:5000/health`
- Additional endpoints as documented in `API/app.py`

### Note Types

Notes can be categorized with different types:
- Type 0: Personal
- Type 1: Work
- Type 2: Shopping

## Unit Tests

The application includes comprehensive unit tests for the security controller. To run the tests:

```bash
# Run all tests
docker-compose exec php php bin/phpunit

# Run specific test class
docker-compose exec php php bin/phpunit tests/Controller/SecurityControllerTest.php
```

### Test Coverage

The SecurityController tests cover the following scenarios:

1. **Registration Tests**
   - Success: User can register with valid credentials
   - Missing Data: Validation fails when required fields are missing
   - Duplicate Email: Prevents duplicate user registration
   - Invalid Email: Handles invalid email formats gracefully

2. **Authentication Tests**
   - Login Success: Validates successful login with correct credentials
   - Invalid Credentials: Handles failed login attempts with incorrect credentials

### Expected Results

All tests should pass with the following assertions:

1. **Registration**
   - Returns HTTP 201 (Created) on successful registration
   - Returns HTTP 400 (Bad Request) when required fields are missing
   - Returns HTTP 409 (Conflict) when trying to register with an existing email
   - Returns HTTP 201 (Created) even with invalid email format (handled by application logic)

2. **Authentication**
   - Returns HTTP 200 (OK) on successful login
   - Returns HTTP 401 (Unauthorized) on failed login attempts

The tests ensure that the security controller functions correctly and handles various edge cases appropriately.

## Python API Tests

The Python API includes a comprehensive test suite that verifies the functionality of the API endpoints. To run the tests:

```bash
# Run all API tests
bash ./API/test_api.sh
```

### Test Coverage

The API tests cover the following scenarios:

1. **Note Management**
   - GET /notes: Retrieve all notes
   - POST /notes: Create a new note
   - GET /notes/{id}: Retrieve a single note
   - PUT /notes/{id}: Update an existing note
   - DELETE /notes/{id}: Delete a note
   - Verify deletion: Ensure deleted note returns 404

2. **Data Validation**
   - Tests include proper JSON validation
   - Verifies correct HTTP status codes
   - Checks response formatting and structure

### Expected Results

The tests verify the following HTTP status codes:

1. **Success Cases**
   - GET /notes: 200 OK
   - POST /notes: 201 Created
   - GET /notes/{id}: 200 OK
   - PUT /notes/{id}: 200 OK
   - DELETE /notes/{id}: 204 No Content

2. **Error Cases**
   - GET /notes/{id} (after deletion): 404 Not Found

The tests ensure that the Python API endpoints function correctly and handle various edge cases appropriately.

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
