
Installation steps

run these commands on your favorite CLI.
1. git clone https://github.com/juaningsnow/praxxys-exam.git
2. composer install
3. npm install
4. if you don't want to create a local database, you can leave it, it is configured to use db4free.net but performance may be slow, just run 
    -php artisan migrate:fresh --seed

   if you want a better performance, you can create a database and set credentials on .env

    DB_HOST=@databaseAddress
    DB_PORT=@databasePort
    DB_DATABASE=@databaseName
    DB_USERNAME=@databaseUsername
    DB_PASSWORD=@databasePassword

    then run the migration and seeders
     -php artisan mgirate --seed or php artisan migrate:fresh --seed

5. php artisan serve
    set the given address on .env as app url for the photos to load
    
    APP_URL=@baseAddress

App Credentials
User: admin or admin@praxxys.ph
Pass: admin