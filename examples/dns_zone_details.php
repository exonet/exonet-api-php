<?php

// Run this script using: php examples/ticket_details.php <YOUR-TOKEN>

require __DIR__.'/../vendor/autoload.php';

$authentication = new Exonet\Api\Auth\PersonalAccessToken($argv[1]);

// Make an Exonet API client that connects to the test API.
$exonetApi = new Exonet\Api\Client($authentication, Exonet\Api\Client::API_TEST_URL);

/*
 * Get a single dns_zone resource. Because depending on who is authorized, the dns_zone IDs change, all dns_zones are
 * retrieved with a limit of 1. From this result, the first DNS zone is used. In a real world scenario you would
 * call something like `$zone = $exonetApi->resource('dns_zones')->id('VX09kwR3KxNo')->get();` to get a single DNS zone
 * by it's ID.
 */
$zones = $exonetApi->resource('dns_zones')->size(1)->get();
// Show this message when there are no zones.
if (empty($zones)) {
    echo 'There are no zones available.';
    exit();
}
$zone = $zones[0];

// Output DNS zone details.
echo sprintf("\nDNS zone:\t%s\n", $zone->attribute('name'));

// Get the records for this zone.
$records = $zone->related('records')->get();
// Show records.
foreach ($records as $record) {
    echo sprintf(
        "%s\t%s\t%s\t%s\n",
        $record->attribute('type'),
        $record->attribute('fqdn'),
        $record->attribute('ttl'),
        $record->attribute('content')
    );
}
echo "\n";
