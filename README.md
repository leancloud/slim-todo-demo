# LeanCloud todo demo for Slim PHP Framework


### Run the application in development

```php
source env.example
composer install
php -S 0.0.0.0:8080 -t public public/index.php

curl localhost:8080/__engine/1/ping
=> {"runtime":"php-7.0.4","version":"0.2.0"}

curl localhost:8080/hello/jack
=> Hello, jack
```
