<?php

use Exonet\Api\Auth\PersonalAccessToken;
use Exonet\Api\Client;

// Run this script using: php examples/dns_zones.php <YOUR-TOKEN>

require __DIR__.'/../vendor/autoload.php';

$authentication = new PersonalAccessToken($argv[1]);

// Make an Exonet API client that connects to the test API.
$exonetApi = new Client($authentication, Client::API_TEST_URL);

// Get all DNS zones, limited to 20.
$zones = $exonetApi->resource('dns_zones')->size(20)->get();

$description = 'DNS zones (max 20):';
echo sprintf(
    "\n%s\n%s\n",
    $description,
    str_repeat('-', strlen($description))
);

foreach ($zones as $zone) {
    echo sprintf("%s - %d records\n", $zone->attribute('name'), count($zone->relationship('records')->getResourceIdentifiers()));
}

echo "\n";
