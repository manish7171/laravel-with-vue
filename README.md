# Upfront

> [!NOTE]  
> All dependencies are managed by Dockerfile and docker-compose.yml file

## System Dependency

- Docker (I am using Docker version 26.1.2)
- Docker image for php, php:8.2-fpm (defined in Dockerfile inside docker folder)
- Docker image for database, mysql:8.0
- Docker image for nginx, nginx:alpine
- Docker image for phpmyadmin phpmyadmin:5.1 (to handle the administration of MySQL over the Web )

## Backend Dependencies

- laravel 11 running on php:8.2
- Using MYSQL version 8 for database
- ORM: Eloquent
- Composer: version 2.7.6
- node: v20.13.1
- npm: 10.5.2

## Frontend Dependencies

- vue: 3.4.27,
- bootstrap: 5.3.3,
- vite
