## Requirements :
Symfony 6.1 - PHP 8.1.0 - MySQL 5.7.36 - Symfony CLI 5.4.13

## Instructions : 
Clone the repo

Create manifest.json file in public/build/  
copy/paste :  
{  
  "build/app.js": "/build/app.123abc.js",  
  "build/dashboard.css": "/build/dashboard.a4bf2d.css",  
  "build/images/logo.png": "/build/images/logo.3eed42.png"  
}  

Create .env.local file at the root of your project and copy/paste the content of the .env file to your new .env.local file  
Replace the identifiers of the database with real values (l.32)  
Create database for Snowtricks 

Open a command line to the root of your project  
Type "composer install"
Type "symfony console doctrine:migrations:migrate"
Go to 127.0.0.1:8000 on the web navigator
