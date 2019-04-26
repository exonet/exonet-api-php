<?php

// Run this script using: php examples/ticket_details.php <YOUR-TOKEN>

require __DIR__.'/../vendor/autoload.php';

$authentication = new Exonet\Api\Auth\PersonalAccessToken($argv[1]);

$exonetApi = new Exonet\Api\Client($authentication);

/*
 * Get a single dns_zone resource. Because depending on who is authorized, the dns_zone IDs change, all dns_zones are
 * retrieved with a limit of 1. From this result, the first DNS zone is used. In a real world scenario you would
 * call something like `$zone = $exonetApi->resource('dns_zones')->get('VX09kwR3KxNo');` to get a single DNS zone
 * by it's ID.
 */
$zones = $exonetApi->resource('dns_zones')->size(1)->get();
// Show this message when there are no zones.
if (empty($zones)) {
    echo 'There are no zones available.';
    die();
}
$zone = $zones[0];

// Output DNS zone details.
echo sprintf("\nDNS zone:\t%s\n", $zone->name);

// Get the records for this zone.
$records = $zone->records()->get();
// Show records.
foreach ($records as $record) {
    echo sprintf(
        "%s  %s   %s   %s\n",
        $record->type,
        $record->fqdn,
        $record->ttl,
        $record->content
    );
}
echo "\n";