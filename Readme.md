# How To Run:  
* Install php through xampp.. need php version 7 and up.
* globally install composer
* clone this repo
* Install node and add path
* go to this project's directory and open cmd
* run cmd 'composer install'
* install node module npm and run 'npm install'
* run cmd 'php artisan key:generate' to generate app-key
* run cmd 'php artisan serve' to start the app at localhost:8000 by default

# Setup DB
* Postgres DB is required for this.
* copy .env.example file in the root and rename it to .env
* In this file change DB_DATABASE,DB_USERNAME,DB_PASSWORD to appropriate values by creating a new postgres db.
* To use Postgres with php some line in php.ini file need to be uncommented.
* In app\Providers\AppServiceProvider.php add Schema::defaultStringLength(191); to the boot method.
* run 'php artisan migrate:fresh' in the cmd for the project root directory.
* app is now setup, add some dummy users, threads, comments,etc.
* Some features like Glogin won't work.

