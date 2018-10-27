/*Main Setup*/
    php artisan make:auth

    php artisan make:migration create_articles_table --create=articles
    php artisan make:migration create_comments_table --create=comments
    php artisan make:seeder ArticlesTableSeeder
    php artisan make:factory ArticleFactory
    php artisan make:model Article
    php artisan make:model Comment
    php artisan make:controller ArticleController --resource
    php artisan make:controller CommentController --resource
    php artisan make:resource Article
    php artisan make:resource Comment

    php artisan migrate[:fresh]
    php artisan db:seed

    php artisan key:generate --show

/*.env Setup*/
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=
    DB_USERNAME=
    DB_PASSWORD=

/*Heroku*/
    git init
    git add .
    git commit -m "msg"
    git remote add heroku {git_url}
    git push heroku master

    heroku run bash
        php artisan migrate[:fresh]
        php artisan db:seed
    OR
    heroku run php artisan migrate[:fresh]
    heroku run php artisan db:seed (required: "fzaninotto/faker")

    add {"APP_NAME",""}, {"DB_PORT",""}, {"APP_KEY",""} to ConfigVars(aka env)

/*Heroku Database*/
    (1)JawsDB MySQL
    (2)ClearDB MySQL

    DB_PORT=3306
    config "config/database.php"

/*Local Apache Lounge Http Server Setup*/
    /*the extensions are in "php/ext"*/
    /*append the following lines to "php/php.ini"*/
    extension=F:\Program Files\PHP 7.2\ext\php_mysqli.dll
    extension=F:\Program Files\PHP 7.2\ext\php_openssl.dll
    extension=php_mbstring.dll
    extension=php_curl.dll
    extension=php_pdo_mysql.dll