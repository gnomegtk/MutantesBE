# Mutantes BE

This project is the result of a selection exam and uses Composer, Silex and PHPUnit.
I used the outdated Silex framework because it's a lightweight framework.

It requires PHP 7 and don't use MVC.

Same functionalities as dependency injection or database configuration could be improved but I wanted to keep it simple.

There is a lack of security with the database configuration with the location of the database configuration.
Maybe in a future version it will be fixed.

## Install

Create the database using the script SQL in the directory sql.
Configure the database in web/index.php:

    new DoctrineServiceProvider(),
    [
        'db.options' => [
            'driver'        => 'pdo_mysql',
            'host'          => '127.0.0.1',
            'dbname'        => 'mutants',
            'user'          => 'root',
            'password'      => 'root',
            'charset'       => 'utf8',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ],
    ]


Install the dependencies using composer:

    composer install
    

The docroot of the webserver must by configurated to:

    web/index.php