<?php

// Run this script using: php examples/dns_action_post.php <YOUR-TOKEN>

use Exonet\Api\Structures\ApiResource;
use Exonet\Api\Structures\ApiResourceIdentifier;

require __DIR__.'/../vendor/autoload.php';

$authentication = new Exonet\Api\Auth\PersonalAccessToken($argv[1]);

// Make an Exonet API client that connects to the test API.
$exonetApi = new Exonet\Api\Client($authentication, Exonet\Api\Client::API_TEST_URL);

$randNr = rand(1, 9999);
$bindZone = '$ORIGIN	dns-action-test-import'.$randNr.'.nl.
dns-action-test-import'.$randNr.'.nl	3600	IN	SOA	ns1.exonet.nl. hostmaster.exonet.nl. 2023122109 10800 3600 604800 3600
dns-action-test-import'.$randNr.'.nl	3600	IN	NS	ns1.exonet.nl.
dns-action-test-import'.$randNr.'.nl	3600	IN	NS	ns2.exonet.nl.
dns-action-test-import'.$randNr.'.nl	3600	IN	NS	ns3.exonet.eu.
dns-action-test-import'.$randNr.'.nl	3600	IN	TXT	"v=spf1 a mx include:_spf.exonet.nl ?all"
dns-action-test-import'.$randNr.'.nl	3600	IN	MX	10 mail.dns-action-test-import'.$randNr.'.nl.
dns-action-test-import'.$randNr.'.nl	3600	IN	A	178.22.60.20
www.dns-action-test-import'.$randNr.'.nl	3600	IN	A	178.22.60.20
mail.dns-action-test-import'.$randNr.'.nl	3600	IN	A	178.22.60.20
dns-action-test-import'.$randNr.'.nl	3600	IN	AAAA	2a00:1e28:3:1573::222
www.dns-action-test-import'.$randNr.'.nl	3600	IN	AAAA	2a00:1e28:3:1573::222
mail.dns-action-test-import'.$randNr.'.nl	3600	IN	AAAA	2a00:1e28:3:1573::222';

// Make the new DNS action that should be added to the zone.
$action = new ApiResource('dns_actions');
$action->attribute('method', 'import_zone');
$action->attribute('data', ['content' => $bindZone]);
$action->relationship('customer', new ApiResourceIdentifier('customers', 'kV2Y0pbZ58xM'));

echo sprintf(
    "%s:\t%s\n",
    $action->attribute('method'),
    json_encode($action->attribute('data')),
);

// POST to API and get the newly created record.
$newAction = $action->post();

echo "\nDNS action created:\n";
$action = $exonetApi->resource('dns_actions', $newAction->id())->get();
echo sprintf(
    "%s:\t%s\n",
    $action->attribute('method'),
    $action->attribute('status'),
);
echo "\n";
