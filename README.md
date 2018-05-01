# psr-15 routing

[![Build Status](https://travis-ci.org/alexpts/php-psr15-routing.svg?branch=master)](https://travis-ci.org/alexpts/php-psr15-routing)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-psr15-routing/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-psr15-routing/?branch=master)
[![Code Climate](https://codeclimate.com/github/alexpts/php-psr15-routing/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-psr15-routing)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-psr15-routing/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-psr15-routing/?branch=master)


#### Install
```composer require alexpts/php-psr15-routing```

Простой роутер с поддержкой PSR-7, PSR-15

#### Возможности
- Простой захват параметров из url
- Использование RegExp для описания роута
- Гибкие группировки для захвата параметров
- Приоритеты роутов
- Высокая скорость работы



### Example
```php
<?php
use PTS\PSR15Routing\CallableAdapter;
use PTS\PSR15Routing\Router;
use PTS\PSR15Routing\Route;
use PTS\PSR15Routing\RouterMiddleware;

$router = new Router;

$router->add('/', new Route('/', new CallableAdapter(function($request) {
    return $response; // $response must be ResponseInterface
})));

$handler = ...; // $handler bust be RequestHandlerInterface
$router->add('/admin', new Route('/', $handler));


// with priority
$router->add('/admin/1', new Route('/', $handler), 100);

// some middleware manager/runner
$app = (new MiddlewareManager)
	->push(new RouterMiddleware($router)