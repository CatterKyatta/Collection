<p align="center">
    <a href="https://koillection.com" target="_blank">
        <img src="https://avatars3.githubusercontent.com/u/38983306?s=200&v=4" width="75" height="75">
    </a>
</p>

<p align="center">
    <img src="https://img.shields.io/github/license/benjaminjonard/koillection.svg" />
    <img src="https://img.shields.io/github/release/benjaminjonard/koillection.svg" />
    <img src="https://img.shields.io/packagist/php-v/benjaminjonard/koillection.svg" />    
    <img src="https://img.shields.io/scrutinizer/g/benjaminjonard/koillection.svg" />
    <img src="https://img.shields.io/travis/benjaminjonard/koillection/master.svg" />    
</p>

# Koillection

Koillection is a self-hosted service allowing users to manage any kind of collection.
It is a project I did for my personal use. But since it can interest some people I decided to release it publicly. 

## Warning

Koillection is still in development. There might be some bugs, missing features and changes in the future.

## Requirements

1. `PHP 7.2` You may need to add the following extensions:
    - `apcu`
    - `cgi`
    - `ctype`
    - `curl`
    - `dom`   
    - `exif`
    - `fileinfo`
    - `fpm`
    - `gd`
    - `iconv`
    - `intl`
    - `json`
    - `mbstring`    
    - `opcache`    
    - `openssl`
    - `pdo`    
    - `pdo_pgsql`    
    - `phar`
    - `session`
    - `simplexml`
    - `sodium`
    - `tokenizer`
    - `xml`
    - `xmlwriter`    
    - `zip`              
2. A webserver such as `Apache2` or `nginx` 
3. A `Postgresql` database

## Installation
### Using git or an archive file

1. `git clone` the repository or download and unzip the project archive
2. Create a `.env.local` file and copy the content of `.env` in it
3. In `.env.local` replace the values between curly braces by your configuration and remove all curly braces
    - `{secret}` -> a random string
    - `{user}` -> your database user
    - `{password}` -> your database password
    - `{host}` -> your database address (ex: 127.0.0.1 or localhost)
    - `{port}` -> your database port (5432 by default for postgres)
    - `{dbname}` -> your database name
    - `{version}` -> your postgres server version (ex: 10.3)    
4. In the project root folder execute `composer install -o`
5. And then `php bin/console doctrine:migrations:migrate`
6. Configure a vhost (you can find an example for nginx in `docs` folder)
7. (optionnal) Copy the values contained in `docs/php.ini` in your own `php.ini`. Not mandatory but can improve performance greatly 

### Using Docker
* https://github.com/benjaminjonard/koillection-docker ->  Comes with PHP FPM and nginx, based on the last release of Koillection.

## Licensing

Koillection is an Open Source software, released under the GNU General Public License v3.0. 
