# Laravel/IBM DB2 Feasibility Test Project

Laravel natively supports SQLite, MySQL(Maria), Postgres, SQL Server(MS SQL). While researching the feasibility of interworking of IBM DB2 and Laravel, I found a project name [Laravel-Doctrine](http://www.laraveldoctrine.org/). The idea behind this project is replacing the Laravel's ORM layer(which is Eloquent) to Doctrine. It is known that Doctrine supports IBM DB2 natively. This project was started as such.

## 1. Install DB2 on Homestead Ubuntu VM

This section explains how to up and run IBM DB2 Database on [Homestead VM (Ubuntu 14.04, PHP7)](https://laravel.com/docs/5.2/homestead).

### 1.1. Download IBM DB2 Express-C

[http://www.ibm.com/developerworks/downloads/im/db2express/](http://www.ibm.com/developerworks/downloads/im/db2express/)

In order to download the file, you will need an IBM account(As easy as getting a SNS account). Even after registration/login, you still need to fill the 'Business Contact Information' form to download the software. Once the download page appears, select 'Download Using Http' tab to download it without installing additional application. 

Choose 64bit Linux version. Right click on the link and copy the URL. Then at the Homestead machine, download the URL with `wget`. The following is my download link, yours may differ. 

```sh
vagrant@homestead:~$ wget https://iwm.dhe.ibm.com/sdfdl/v2/regs2/db2pmopn/db2_v105/expc/Xa.2/Xb.aA_60_-i7wmrzQQIq8r9irKm9qe-5oKSmz89VXmFlaU/Xc.db2_v105/expc/v10.5_linuxx64_expc.tar.gz/Xd./Xf.LPr.D1vk/Xg.8614361/Xi.swg-db2expressc/XY.regsrvs/XZ.NFMI7vG6OiKBxMev0FhONJzd_A0/v10.5_linuxx64_expc.tar.gz
```

Extract it. Extracted directory is `expc`.

```sh
vagrant@homestead:~$ tar xvfp v10.5_linuxx64_expc.tar.gz
```

### 1.2. Create System Accounts

Unlike MySQL, DB2 uses system account as its database login account. I don't know what account is for what, so let's just follow the instruction.

Create groups.

```sh
vagrant@homestead:~$ sudo groupadd db2iadm1
vagrant@homestead:~$ sudo groupadd db2fsdm1
vagrant@homestead:~$ sudo groupadd dasadm1
```

Create users.

```sh
vagrant@homestead:~$ sudo useradd db2inst1 -g db2iadm1 -m
vagrant@homestead:~$ sudo useradd db2fenc1 -g db2fsdm1 -m
vagrant@homestead:~$ sudo useradd dasusr1 -g dasadm1 -m
```

Set default password for each user.

```sh
vagrant@homestead:~$ sudo passwd db2inst1
# Enter new UNIX password:
# Retype new UNIX password:
# passwd: password updated successfully

vagrant@homestead:~$ sudo passwd db2fenc1
vagrant@homestead:~$ sudo passwd dasusr1
```

### 1.3. Run The Install Process

DB2 requires system-wide prerequisites(dependencies). To check that, do the following command.

```sh
vagrant@homestead:~$ ./expc/db2prereqcheck
# Requirement not matched for DB2 database "Server" . Version: "10.5.0.7".
# Summary of prerequisites that are not met on the current system:
#    DBT3514W  The db2prereqcheck utility failed to find the following 32-bit library file: "/lib/i386-linux-gnu/libpam.so*".
# 
# DBT3514W  The db2prereqcheck utility failed to find the following 32-bit library file: "libstdc++.so.6".
# 
# Requirement not matched for DB2 database "Server" with pureScale feature . Version: "10.5.0.7".
# Summary of prerequisites that are not met on the current system:
#    DBT3514W  The db2prereqcheck utility failed to find the following 32-bit library file: "/lib/i386-linux-gnu/libpam.so*".
# 
# DBT3514W  The db2prereqcheck utility failed to find the following 32-bit library file: "libstdc++.so.6".
```

Immediately, DB2 complains that `libpam.so` and `libstdc.so` are missed. After some Googling, I found that both of them are 32bit library. Below is the instruction how to install them.

```sh
vagrant@homestead:~$ sudo dpkg --add-architecture i386
vagrant@homestead:~$ sudo apt-get update
vagrant@homestead:~$ sudo apt-get install libpam0g:i386 libstdc++6:i386
```

`./expc/db2prereqcheck` now passes. 

Let's run the DB2 installer. Prepending `sudo` is important. With `sudo` it installs in `/opt/ibm/db2/V10.5`, but without it in `~/sqllib`. I tried non-sudo install, I may not know how to tackle post-install process, but it produces problems.

```sh
vagrant@homestead:~$ sudo ./expc/db2_install
# Default directory for installation of products - /opt/ibm/db2/V10.5
# ***********************************************************
# Install into default directory (/opt/ibm/db2/V10.5) ? [yes/no]
#
# ...
#
# The execution completed successfully.
# 
# For more information see the DB2 installation log at
# "/tmp/db2_install_vagrant.log".
# 
# DBI1272I  To start using the DB2 instance vagrant, you must
#       set up the DB2 instance environment by sourcing db2profile or
#       db2cshrc in the sqllib directory, or you can open a new login
#       window of the DB2 instance user.
# 
# Explanation:
# 
# The DB2 instance cannot be used before db2profile (for Bourne or Korn
# shell users) or db2cshrc (for C shell users) is sourced.
# 
# User response:
# 
# To set up the DB2 instance environment, you can open a new login window
# under the ID that owns the DB2 instance, or source the DB2 instance
# environment by running the appropriate following command under the ID
# that owns the DB2 instance:
# 
# . $HOME/sqllib/db2profile
# 
# source $HOME/sqllib/db2cshrc
# 
#  where $HOME represents the home directory of the user ID that owns the
# DB2 instance.
```

Validate the installation(This is optional).

```sh
vagrant@homestead:~$ /opt/ibm/db2/V10.5/bin/db2val
# BI1379I  The db2val command is running. This can take several minutes.
# 
# DBI1335I  Installation file validation for the DB2 copy installed at
#       /opt/ibm/db2/V10.5 was successful.
# 
# DBI1344E  The validation tasks of the db2val command failed. For
#       details, see the log file /tmp/db2val-160516_124450.log.
```

### 1.4. Create DB Instance

The following command creates db2 instance for db2inst1 user.

```sh
vagrant@homestead:~$ sudo /opt/ibm/db2/V10.5/instance/db2icrt -d -a server -u db2fenc1 db2inst1
# The execution completed successfully.
# For more information see the DB2 installation log at "/tmp/db2icrt.log.6939".
# DBI1070I  Program db2icrt completed successfully.

vagrant@homestead:~$ /opt/ibm/db2/V10.5/bin/db2ilist
# db2inst1
```

## 2. Install Project

### 2.1. Clone & Install Dependencies

```sh
vagrant@homestead:~$ git clone git@github.com:appkr/laravel-doctrine-db2.git
vagrant@homestead:~$ cd laravel-doctrine-db2 
vagrant@homestead:~$ composer install
vagrant@homestead:~$ chmod -R 777 storage
```

Create `.env` settings.

```sh
vagrant@homestead:~$ cp .env.example .env
vagrant@homestead:~$ php artisan key:generate
```

Fill out IBM DB2 connection information.

```sh
# .env

DB_CONNECTION=db2
DB_HOST=127.0.0.1
DB_DATABASE=doctrine
DB_USERNAME=db2inst1
DB_PASSWORD=secret
```

### 2.2. Install `ibm_db2` PHP Extensions

Doctrine DBAL(DB Abstraction Layer) depends on `ibm_db2` extensions. 

```sh
vagrant@homestead:~$ sudo pecl install ibm_db2
# DB2 Installation Directory? : /opt/ibm/db2/V10.5/
```

During the install process, an interactive question like `DB2 Installation Directory?` may pops up. Answer `/opt/ibm/db2/V10.5/`.

When pecl download and compile finishes, we need to enable the extension.

```sh
vagrant@homestead:~$ sudo echo "extension=ibm_db2.so" > /etc/php/7.0/mods-available/ibm_db2.ini
vagrant@homestead:~$ sudo phpenmod ibm_db2
```

Restart web server and FPM.

```sh
vagrant@homestead:~$ sudo service nginx restart
vagrant@homestead:~$ sudo service php7.0-fpm restart
```

### 2.3. Create Database & Schema & Seed

Login as `db2inst1` user and start a DB2 instance.

```sh
vagrant@homestead:~$ su - db2inst1
# Password:

db2inst1@homestead:~$ db2start
# SQL1063N  DB2START processing was successful.
```

Start DB2 console.

```sh
db2inst1@homestead:~$ db2
# (c) Copyright IBM Corporation 1993,2007
# Command Line Processor for DB2 Client 10.5.7
# 
# You can issue database manager commands and SQL statements from the command
# prompt. For example:
#     db2 => connect to sample
#     db2 => bind sample.bnd
# 
# For general help, type: ?.
# For command help, type: ? command, where command can be
# the first few keywords of a database manager command. For example:
#  ? CATALOG DATABASE for help on the CATALOG DATABASE command
#  ? CATALOG          for help on all of the CATALOG commands.
# 
# To exit db2 interactive mode, type QUIT at the command prompt. Outside
# interactive mode, all commands must be prefixed with 'db2'.
# To list the current command option settings, type LIST COMMAND OPTIONS.
# 
# For more detailed help, refer to the Online Reference Manual.
```

Let's create database `doctrine`(I tried `laravel_doctrine`, `laraveldoctrine` with/without quote, but was not possible).

```sh
db2 => create database doctrine
# DB20000I  The CREATE DATABASE command completed successfully.
```

To create table schema, we have to return back to `vagrant` user. Laravel-doctrine provides special artisan command `doctrine:schema:create` to make table schema. There are couple of ways(drivers) to define schema with Laravel-Doctrine: `fluent`, `annotations`, `yaml`, `xml`, `config`, `static_php`, `php`. This project relies on `fluent` driver(For more information about it, read the [documentation](http://www.laraveldoctrine.org/docs/1.1/fluent)). 

```sh
db2inst1@homestead $ su - vagrant # or just exit
vagrant@homestead:~$ php laravel-doctrine-db2/artisan doctrine:schema:create
```

Seed the test data.

```sh
vagrant@homestead:~$ php laravel-doctrine-db2/artisan db:seed
```

> **MySQL Database & Table Creation**
> 
> We assume mysql user is 'homestead'.
> 
> ```sh
> $ mysql -uroot -p
> mysql> CREATE DATABASE doctrine;
> mysql> GRANT ALTER, CREATE, INSERT, SELECT, DELETE, REFERENCES, UPDATE, DROP, EXECUTE, LOCK TABLES, INDEX ON doctrine.* TO 'homestead';
> mysql> FLUSH PRIVILEGES;
> mysql> quit
> ```

### 2.4. Test

Browse to http://localhost:8000 (or your project's url). And test it manually.

PHPUnit works against SQLite (what it means is that the test does not touch MySQL or DB2). 

```
$ vendor/bin/phpunit
```

### 2.5. Available Routes

```
+----------+-------------------------+-----------------------------------------------------------------+------------+
| Method   | URI                     | Action                                                          | Middleware |
+----------+-------------------------+-----------------------------------------------------------------+------------+
| GET|HEAD | /                       | Closure                                                         | web        |
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

## 3. Summary & Todo

Testing done against MySql/DB2. 

Laravel-Doctrine|MySql|IBM DB2|Memo
---|---|---|---
CRUD|Tested|Tested|&nbsp;
User Registration|Tested|Tested|&nbsp;
User Authentication|Tested|Tested|&nbsp;
Password Reset|Tested|Tested|&nbsp;
Authorization|Tested|Tested|&nbsp;
Object Relationship|Tested|Tested|Only one-to-many
Migration|Tested|Tested|`artisan doctrine:schema:create` with `annotation` & `fluent` mapping driver.
Seed|Tested|Tested|`entity()` instead of `factory()`

Next step is to exploit Doctrine usage.

## 4. Basic DB2 Command

Note that DB2 command should be issued as `db2inst1` user.

Description|IBM DB2|MySQL equivalent
---|---|---
Start DB Instance|`db2start`|`sudo service mysql start`
Stop DB Instance|`db2stop`|`sudo service mysql stop`
Start Client Console|`db2`|`mysql -uhomestead -p`
Stop Client Consloe|`exit`, `quit`, <kbd>Ctrl</kbd>+<kbd>c</kbd>, <kbd>Ctrl</kbd>+<kbd>d</kbd>|<kbd>Ctrl</kbd>+<kbd>d</kbd>
List Databases|`list db directory`|`SHOW DATABASES;`
Select Database|`connect to doctrine`|`USE doctrine;`
List Tables|`list tables`|`SHOW TABLES;`
Print Table Schema|`describe table tasks`|`DESCRIBE tasks;`
