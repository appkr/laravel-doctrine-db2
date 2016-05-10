# DB2 Feasibility Test Project with Laravel-Doctrine

## How to Install

### 1. Clone & Install Dependencies

```sh
$ git clone git@github.com:appkr/laravel-doctrine-db2.git
$ cd laravel-doctrine-db2 
$ composer install
```

Create and modify `.env` settings.

```sh
$ cp .env.example .env
$ php artisan key:generate
```

### 2. Create Database

We assume mysql user is 'homestead'.

```sh
$ mysql -uroot -p
mysql> CREATE DATABASE laravel_doctrine;
mysql> GRANT ALTER, CREATE, INSERT, SELECT, DELETE, REFERENCES, UPDATE, DROP, EXECUTE, LOCK TABLES, INDEX ON laravel_doctrine.* TO 'homestead';
mysql> FLUSH PRIVILEGES;
```

### 3. Local Server

```sh
$ php artisan serve
```

Browse to http://localhost:8000

### 4. Avaialable Routes

```
+----------+-------------------------+-----------------------------------------------------------------+------------+
| Method   | URI                     | Action                                                          | Middleware |
+----------+-------------------------+-----------------------------------------------------------------+------------+
| GET|HEAD | /                       | Closure                                                         | web        |
| GET|HEAD | example                 | Closure                                                         | web        |
| GET|HEAD | home                    | App\Http\Controllers\HomeController@index                       | web,auth   |
| GET|HEAD | login                   | App\Http\Controllers\Auth\AuthController@showLoginForm          | web,guest  |
| POST     | login                   | App\Http\Controllers\Auth\AuthController@login                  | web,guest  |
| GET|HEAD | logout                  | App\Http\Controllers\Auth\AuthController@logout                 | web        |
| POST     | password/email          | App\Http\Controllers\Auth\PasswordController@sendResetLinkEmail | web,guest  |
| POST     | password/reset          | App\Http\Controllers\Auth\PasswordController@reset              | web,guest  |
| GET|HEAD | password/reset/{token?} | App\Http\Controllers\Auth\PasswordController@showResetForm      | web,guest  |
| GET|HEAD | register                | App\Http\Controllers\Auth\AuthController@showRegistrationForm   | web,guest  |
| POST     | register                | App\Http\Controllers\Auth\AuthController@register               | web,guest  |
| POST     | task                    | Closure                                                         | web        |
| GET|HEAD | task/{id}               | Closure                                                         | web        |
| DELETE   | task/{id}               | Closure                                                         | web        |
| GET|HEAD | task/{id}/update        | Closure                                                         | web        |
+----------+-------------------------+-----------------------------------------------------------------+------------+
```

## Todo

Test done against MySql. Next step is testing against DB2.


