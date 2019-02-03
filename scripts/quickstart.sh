#!/bin/bash
echo "------------------------------"
echo "QUICKSTART INSTALL OTP-LARAVEL"
echo ""
echo "This script will create a NEW LARAVEL project with otp-laravel"
echo "  using a SQLITE database "
echo "  and initialize a local GIT repository."
echo ""
echo "Usage is intended for GNU/Linux."
echo "Usage is at your own risk."
echo "------------------------------"
echo ""
echo -n "Directory name for your project: "
read PROJECT

if [ -d $PROJECT ]; then
  echo "Project exists already!"
  exit
fi

mkdir "$PROJECT"
cd "$PROJECT"

laravel new
chmod +x ./artisan

echo "<QS> Git init, first commit..."
git init
git add * .gitignore .gitattr* .editorc* .env.example
git commit -m 'new laravel'
git tag -a "v0.0.1" -m "Version 0.0.1 (auto)"

cp -p .env.example .env
sed -i "s/^APP_NAME=.*/APP_NAME=$PROJECT/" .env
./artisan key:generate

echo "<QS> Setup database..."
touch database/app.sqlite
echo "database/app.sqlite" >> .gitignore
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/" .env
sed -i "s#^DB_DATABASE=.*#DB_DATABASE=$PWD/database/app.sqlite#" .env
./artisan migrate


echo "<QS> Composer: Require the OTP package..."
# Add repo while not published at packagist...
composer config repo.otp-laravel vcs https://github.com/dcvn/otp-laravel
composer require "dcvn/otp-laravel"
./artisan migrate

cat ./vendor/dcvn/otp-laravel/scripts/dotenv-otp >> .env
sed -i "s/^OTP_ISSUER=.*/OTP_ISSUER=$PROJECT/" .env
sed -i "s/^OTP_ALGORITHM=.*/OTP_ALGORITHM=sha512/" .env

echo "<QS> Make auth files..."
./artisan make:auth

mkdir _notes
echo ".tags" >> .gitignore
echo ".tags1" >> .gitignore
echo "_notes/" >> .gitignore

echo "<QS> Git commit updates..."
git add * .gitignore
git commit -m "add otp package and laravel auth files"
git tag -a "v0.0.2" -m "Version 0.0.2 (auto)"

echo "<QS> Editing files for OTP..."
FILE=app/Http/Controllers/Auth/LoginController.php
sed -i "s/^use .*AuthenticatesUsers;$/use Dcvn\\\\Otp\\\\Http\\\\Controllers\\\\AuthenticatesUsers;/" $FILE

FILE=app/User.php
sed -i '/^}$/i \
    protected $dates = [\
        "otp_secret_set_at",\
    ];' $FILE

FILE=resources/views/home.blade.php
sed -i $'/<div class="container">/a\\    @include\("otp::flash"\)\\n' $FILE
HTML='<p><a href="{{ route("otp.scan", Auth::user()) }}">Setup OTP</a></p>'
sed -i "/You are logged in/a $HTML" $FILE

echo "<QS> Copy seeder, seed database users"
cp -p ./vendor/dcvn/otp-laravel/scripts/UsersTableSeeder.php ./database/seeds/UsersTableSeeder.php

composer dump-autoload
./artisan db:seed --class UsersTableSeeder

echo "<QS> Git commit updates..."
git add *
git commit -m "setup otp files"
git tag -a "v0.0.3" -m "Version 0.0.3 (auto)"


echo "<QS> Installation ready, start server..."
echo ./artisan serve --host localhost --port 8081
