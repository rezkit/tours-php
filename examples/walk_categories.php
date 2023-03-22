#!/usr/bin/php
<?php

use Dotenv\Dotenv;
use RezKit\Tours\Client;

require_once('vendor/autoload.php');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$client = new Client($_ENV['API_KEY'], ['base_uri' => $_ENV['API_HOST']]);

$categories = $client->holidays()->categories()->list();

$ancestry = [];
$anames = [];
$visited  = [];

foreach ($categories as $c) {
    $visited[] = $c->getId();

    if ($p = $c->getParentId()) {
        // If this is a parent already visited (traverse-up or traverse-across)
        if (in_array($p, $ancestry)) {
            // Pop the ancestry until the last-visited parent is the current parent
            // on traverse-up. No effect on traverse-across.
            while(current(array_reverse($ancestry)) !== $p) {
                array_pop($ancestry);
                array_pop($anames);
            }
            array_pop($anames);
        } else {
            // Append the new parent (traverse-down)
            $ancestry[] = $p;
        }
    } else {
        // No ancestry, reset.
        $ancestry = [];
        $anames = [];
    }

    $anames[] = $c->getName();

    echo implode( ' → ', $anames) . "\n";
}
