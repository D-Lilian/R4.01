@echo on
@php bin\console doctrine:database:create --if-not-exists 2>NUL
php bin\console doctrine:schema:drop --full-database --force
php bin\console doctrine:schema:create
php bin\console doctrine:fixtures:load --append
php -S 0.0.0.0:8000 -t public 
bin/console dbal:run-sql "INSERT INTO admin (id, username, roles, password) values (0, 'root', '[\"ROLE_ADMIN\"]', '\$2y\$13\$W4BNB29zUfRR.W0WXPa3EeNQ04T/gDIHJL/LCBlf4O4orbL.JN106');"