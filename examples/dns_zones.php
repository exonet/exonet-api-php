<?php

// Run this script using: php examples/dns_zones.php <YOUR-TOKEN>

require __DIR__.'/../vendor/autoload.php';

$authentication = new Exonet\Api\Auth\PersonalAccessToken($argv[1]);

$exonetApi = new Exonet\Api\Client($authentication, 'https://test-api.exonet.nl');

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
