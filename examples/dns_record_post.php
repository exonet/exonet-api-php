<?php

// Run this script using: php examples/dns_record_post.php <YOUR-TOKEN>

use Exonet\Api\Structures\Resource;

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
    die();
}
$zone = $zones[0];

// Output DNS zone details.
echo sprintf("\nDNS zone:\t%s\n", $zone->attribute('name'));

// User must confirm adding DNS record.
echo sprintf("\nWARNING: this command will add a TXT record to the DNS zone %s, continue? [y/n] ", $zone->attribute('name'));
if ('Y' !== strtoupper(trim(fgets(STDIN)))) {
    echo 'Cancel.';
    exit();
}

// Make the new DNS record that should be added to the zone.
$record = new Resource('dns_records');
$record->attribute('type', 'TXT');
$record->attribute('name', 'subdomain');
$record->attribute('content', '"Exonet API script '.microtime().'"');
$record->attribute('ttl', 900);
$record->relationship('zone')->setResourceIdentifiers(
    $zone
);

echo sprintf(
    "%s\t%s\t%s\t%s\n",
    $record->attribute('type'),
    $record->attribute('name'),
    $record->attribute('ttl'),
    $record->attribute('content')
);

// POST to API and get the newly created record.
$newRecord = $record->post();

echo "\nDNS record created:\n";
echo sprintf(
    "%s\t%s\t%s\t%s\n",
    $newRecord->attribute('type'),
    $newRecord->attribute('fqdn'),
    $newRecord->attribute('ttl'),
    $newRecord->attribute('content')
);

echo sprintf("\nNew DNS record has ID %s", $newRecord->id());

// Ask user if record should be deleted.
echo sprintf("\nDo you want to delete this record? [y/n] ");
if ('Y' !== strtoupper(trim(fgets(STDIN)))) {
    echo "\nDon't delete record\n";
    exit();
}

echo 'Delete record';
$newRecord->delete();
echo "\nDone.\n";
