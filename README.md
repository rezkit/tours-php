Tour Manager PHP
================

PHP API Client for RezKit Tour Manager.


Installation
------------

### Requirements

* PHP >= 7.4
* Composer
* ext-curl

Install with composer:

    composer require rezkit/tours

Usage
-----

```php
use RezKit\Tours\Client;

$apiKey = YOUR_API_KEY;
$client = new Client($apiKey);
```

Features
--------

### Typed Requests/Responses
All requests and responses are typed 

### Pagination
Paginated responses implement `Iterator` and `Countable` so you can iterate over them seamlessly without
having to manually make requests for each page of data:

```php
$holidays = $client->holidays();

// Will print out the name of every holiday.
foreach($holidays as $holiday) {
    echo $holiday->name . "\n";
}
```


Advanced Usage
--------------

### Custom Client-Side Middleware / Overrides

The `Client` constructor accepts an array of GuzzlePHP options, including a `handler` option which accepts
a `HandlerStack`. By supplying a custom `HandlerStack` additional middleware can be added.

```php
$stack = \GuzzleHttp\HandlerStack::create();
$stack->setHandler(new \GuzzleHttp\Handler\CurlHandler());
$stack->push(my_custom_middleware());

$client = new Client($apiKey, ['handler' => $stack]);
```
