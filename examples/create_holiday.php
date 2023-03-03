<?php
use Dotenv\Dotenv;
use RezKit\Tours\Client;
use RezKit\Tours\Requests\CreateHoliday;

require_once('vendor/autoload.php');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Client($_ENV['API_KEY'], ['base_uri' => $_ENV['API_HOST']]);

$params = new CreateHoliday();

echo 'Holiday Code*: ';
$params->code = fgets(STDIN);

echo 'Holiday Name*: ';
$params->name = fgets(STDIN);

echo 'Creating Holiday: ' .$params->name . "\n";

$holiday = $client->holidays()->create($params);

echo 'ID = ' . $holiday->getId() . "\n";
