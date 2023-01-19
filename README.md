## Requirements :
Symfony 6.1 - PHP 8.1.0 - MySQL 5.7.36 - Symfony CLI 5.4.13

## Instructions : 
Clone the repo

Create manifest.json file in public/build/  
copy/paste :  
```json
{  
  "build/app.js": "/build/app.123abc.js",  
  "build/dashboard.css": "/build/dashboard.a4bf2d.css",  
  "build/images/logo.png": "/build/images/logo.3eed42.png"  
}  
```

Create .env.local file at the root of your project and copy/paste the content of the .env file to your new .env.local file  
Replace the identifiers of the database with real values (l.32)  
```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/databaseName?serverVersion=8.0.29&charset=utf8mb4"
```
Create database for Snowtricks 

Open a command line to the root of your project  
```bash
composer install
symfony console doctrine:migrations:migrate
symfony serve
```
Go to 127.0.0.1:8000 on the web navigator

## DataFixtures : 
Open a command line to the root of your project  
```bash
symfony console doctrine:fixtures:load
```

## Create an account  
First, you have to configure .env.local file (l.50: MAILER_DNS), you need a gmail account  
```env
MAILER_DSN=gmail+smtp://youremail@adresse.com:xfrebanwsqsmuomx@default
```
Now you can create an account on the website  
You can't create an account directly in the database because the password won't be hashed  
