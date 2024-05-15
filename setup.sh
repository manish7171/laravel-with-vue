#!/usr/bin/env bash
#
# make new directory call upfront in home directory
echo ">>> Creating upfront folder in home directory... "
mkdir -p ~/upfront

# Change directory to "upfront"
cd ~/upfront

# Git clone repository in the "upfront" folder
echo ">>> git clone into upfront directory... "
git clone https://github.com/manish7171/laravel-with-vue.git .

# Build containers
echo ">>> building and running docker containers... "
cd ~/upfront/docker
docker compose up build --build-arg USER=www-data --build-arg USER_ID=1000 --build-arg GROUP_ID=1000
docker compose up -d
cd ~/upfront

# create a datbase
# Define variables
echo ">>> Creating Database... "
DB_CONTAINER_NAME="db_jobleads"
DB_NAME="job_leads"
DB_USER="root"
DB_PASS="root"

# Run MySQL client command inside Docker container to create database
docker exec -i $DB_CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

# Optional: Create a database user and grant privileges
docker exec -i $DB_CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$DB_PASS';"
docker exec -i $DB_CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';"
docker exec -i $DB_CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "FLUSH PRIVILEGES;"

echo ">>> Database '$DB_NAME' created successfully..."

# Copy contents of .env.example to .env file
cp ~/upfront/src/.env.example ~/upfront/src/.env

APP_CONTAINER_NAME="app_jobleads"
# composer install
echo ">>> Running composer install... "
docker exec -i $APP_CONTAINER_NAME composer install

#  npm install
echo ">>> Running npm install... "
docker exec -i $APP_CONTAINER_NAME npm install

# compile vue files
echo ">>> Build frontend... "
docker exec -i $APP_CONTAINER_NAME npm run build

# build table
echo ">>> Create User table... "
docker exec -i $APP_CONTAINER_NAME php artisan migrate

# seed user table
echo ">>> Seeding User table... "
docker exec -i $APP_CONTAINER_NAME php artisan db:seed

echo ">>> Open http://localhost:8000/frontend for viewing project"
echo ">>> Open http://localhost:8888 for viewing database"
echo ">>> Open http://localhost:8888/api/users for viewing user list api"
