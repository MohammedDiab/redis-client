# Redis Client Library
**Basic Redis Client Library. so far has four commands (SET,GET,SADD,SMEMBERS)**

# Installation
## PHP
**php 5.5 or larger** 
## Composer
from the library folder, paste the following commands
```
wget -nc http://getcomposer.org/composer.phar
mv composer.phar /usr/local/bin/composer
composer install
composer  dump-autoload -o
```
## Running Examples 
```
cd Examples
php redis_set_get.php
```
## Running Tests
```
cd vendor/bin
./phpunit --bootstrap ../autoload.php --no-configuration ../../tests
```