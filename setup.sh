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
echo ">> check current directory"
pwd
docker compose build --build-arg USER=www-data --build-arg GROUP=www-data --build-arg USER_ID=1000 --build-arg GROUP_ID=1000
docker compose up -d
cd ~/upfront
pwd
echo ">> check current directory"
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

# Reset permissions to 644 for files and 755 for folders
cd ~/upfront/src

#sudo find . -type f -exec chmod 664 {} \;
#sudo find . -type d -exec chmod 775 {} \;

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

#make storage and bootstrap/cache folder writable
echo ">>> Please give permission to write in storage and bootstrap/cache folder"
sudo chmod -R o+w ~/upfront/src/storage ~/upfront/src/bootstrap/cache
echo " !!! Project installation finished !!!"
echo ">>> Open http://localhost:8000/frontend for viewing project"
echo ">>> Open http://localhost:8888 for viewing database"
echo ">>> Open http://localhost:8888/api/users for viewing user list api"
