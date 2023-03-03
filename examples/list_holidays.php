#!/usr/bin/php
<?php

/**
 * Lists the available holidays, versions and departures, in a tree-like format.
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
$params->limit = 50;
$holidays = $client->holidays()->list($params);

foreach($holidays as $holiday) {

    $versions = $client->holidays()->versions($holiday->getId())->list($params);

    echo '- ' . $holiday->getId() . "\n";

    foreach($versions as $version) {
        echo "  - " . $version->getId() . "\n";
    }

}
