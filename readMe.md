# requirement 
- php 8.3
- Symfony cli 5.8
- composer 2.7

# Initialization

(1) git clone https://github.com/LucasBil/PROJ531_Quiz
(2) composer install
(3) creer un fiichier data.db dans /var
(4) php bin/console doctrine:schema:update --force
(5) php bin/console doctrine:fixture:load
(5.1) > yes

# launch

symfony serve:start
