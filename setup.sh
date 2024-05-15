#!/usr/bin/env bash
#
# make new directory call upfront in home directory
echo ">>> Creating upfront folder in home directory... "
mkdir -p ~/upfront

# Change directory to "upfront"
cd ~/upfront

# Git clone repository in the "upfront" folder
#echo ">>> git clone into upfront directory... "
#git clone https://github.com/manish7171/laravel-with-vue.git .

# Build containers
echo ">>> building and running docker containers... "
cd ~/upfront/docker
docker compose up --build -d
cd ~/upfront

# create a datbase
# Define variables
echo ">>> Creating Database... "
CONTAINER_NAME="db_jobleads"
DB_NAME="job_leads"
DB_USER="root"
DB_PASS="root"

# Run MySQL client command inside Docker container to create database
docker exec -i $CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"

# Optional: Create a database user and grant privileges
docker exec -i $CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "CREATE USER IF NOT EXISTS '$DB_USER'@'%' IDENTIFIED BY '$DB_PASS';"
docker exec -i $CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';"
docker exec -i $CONTAINER_NAME mysql -u root -p"$DB_PASS" -e "FLUSH PRIVILEGES;"

echo ">>> Database '$DB_NAME' created successfully..."

# Copy contents of .env.example to .env file
cp ~/upfront/src/.env.example ~/upfront/src/.env

# composer install
echo ">>> Running composer install... "
docker exec app_jobleads composer install

#  npm install
echo ">>> Running npm install... "
docker exec app_jobleads npm install

# compile vue files
echo ">>> Build frontend... "
docker exec app_jobleads npm run build

# build table
echo ">>> Create User table... "
docker exec app_jobleads php artisan migrate

# seed user table
echo ">>> Seeding User table... "
docker exec app_jobleads php artisan db:seed

echo ">>> Open http://localhost:8000/frontend for viewing project"
echo ">>> Open http://localhost:8888 for viewing database"
echo ">>> Open http://localhost:8888/api/users for viewing user list api"
