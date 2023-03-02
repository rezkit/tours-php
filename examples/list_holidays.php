#!/usr/bin/php
<?php

/**
 * Lists the available holidays.
 *
 * Makes use of the `Iterator` behaviour of paginated responses to automatically fetch all items.
 */

use Dotenv\Dotenv;
use RezKit\Tours\Client;
use RezKit\Tours\Requests\ListHolidays;

require_once('vendor/autoload.php');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Client($_ENV['API_KEY'], ['base_uri' => $_ENV['API_HOST']]);

$params = new ListHolidays();
$params->limit = 5;
$params->name = 'E';
$holidays = $client->holidays()->list($params);

echo implode("\t", ["ID", "Code", "Name"]) . "\n";
foreach($holidays as $holiday) {
    echo implode("\t", [$holiday->getId(), $holiday->getCode(), $holiday->getName()]) . "\n";
}
