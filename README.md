## Prerequisites

- Docker
- Docker Compose
- Git, for cloning the repository

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/reiware/test-assignment.git
```

### 2. Set up environment variables

   ```bash
   cd test-assignment
   
   cp .env.example .env
   ```

Then update .env if needed.

### 3. Build and start the containers

   ```bash
   docker compose up -d --build
   ```
On the first start, the application container will automatically:
 - install PHP dependencies
 - install frontend dependencies
 - build frontend assets
 - generate the application key if it is missing
 - run database migrations
## Accessing the Application

Once all containers are up and running, you can access the application at:

```
http://localhost:8080
```

Additional services:

```
Mailpit:   http://localhost:8025
RabbitMQ:  http://localhost:15672
```

## Container Management

- **Start containers**
  ```bash
  docker compose up -d
  ```

- **Stop containers**
  ```bash
  docker compose down
  ```

- **View logs**
  ```bash
  docker compose logs -f
  ```

- **Access a container shell**
  ```bash
  docker compose exec app bash
  ```

## Useful Commands
Run migrations manually:

```
docker compose exec app php artisan migrate
```

Fresh migrations:

```
docker compose exec app php artisan migrate:fresh
```

Clear Laravel cache:

```
docker compose exec app php artisan optimize:clear
```

Rebuild frontend assets manually:

```
docker compose exec app npm run build
```

Restart queue worker:

```
docker compose restart queue
```
