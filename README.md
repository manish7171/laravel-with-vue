# Upfront

> [!NOTE]  
> All dependencies are managed by Dockerfile and docker-compose.yml file
> Docker service needs to be running.

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

## Installation guide

### By running setup.sh script

- Find the setup.sh file which I have send
- Make it executable run giving following command
  ` chmod +x setup.sh`
- Then execute setup.sh
  ` ./setup.sh`
- Note that docker service must be running. If docker command needs sudo then you would want to run sudo ./setup.sh

### Manual setup process

- create a directory where you want to git clone.
- cd into the directory and git clone https://github.com/manish7171/laravel-with-vue.git .
- cd into docker folder and run following commands

````
``` docker compose build
    docker compose up -d
    docker exec -i db_jobleads mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS job_leads;"
# Optional: Create a database user and grant privileges
    docker exec -i db_jobleads mysql -u root -proot -e "CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'root';"
    docker exec -i db_jobleads mysql -u root -proot -e "GRANT ALL PRIVILEGES ON root.* TO 'root'@'%';"
    docker exec -i db_jobleads mysql -u root -proot -e "FLUSH PRIVILEGES;"

    docker exec -i app_jobleads composer install
    docker exec -i app_jobleads npm install
    docker exec -i app_jobleads npm run build
    docker exec -i app_jobleads php artisan migrate
    docker exec -i app_jobleads php artisan db:seed

````

-- For laravel to run properly two folders needs to be writable.

> [!NOTE]  
> Directory structure here

```
   sudo chmod -R o+w ~/upfront/src/storage ~/upfront/src/bootstrap/cache
```

### Urls

- http://localhost:8000/frontend
  - For viewing project
- http://localhost:8888
  - For viewing database
- http://localhost:8888/api/users
  - for viewing user list api
