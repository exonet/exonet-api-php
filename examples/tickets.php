<?php

// Run this script using: php examples/tickets.php <YOUR-TOKEN>

use Exonet\Api\Auth\PersonalAccessToken;
use Exonet\Api\Client;
use Exonet\Api\Structures\ApiResourceSet;

require __DIR__.'/../vendor/autoload.php';

$authentication = new PersonalAccessToken($argv[1]);

// Make an Exonet API client that connects to the test API.
$exonetApi = new Client($authentication, Client::API_TEST_URL);

// Show all tickets, limited to 10.
$allTickets = $exonetApi->resource('tickets')->size(10)->get();
renderTickets('ID and subject of all tickets (with a limit of 10):', $allTickets);

// Show all tickets that are considered 'open' according to Exonet.
$openTickets = $exonetApi->resource('tickets')->filter('open')->get();
renderTickets('ID and subject of open tickets:', $openTickets);

/**
 * Helper function to render ticket lists.
 *
 * @param string         $description The description of the list.
 * @param ApiResourceSet $ticketList  The resource set containing ticket resources.
 */
function renderTickets(string $description, ApiResourceSet $ticketList): void
{
    echo sprintf("\n%s (%d)\n%s\n", $description, count($ticketList), str_repeat('-', strlen($description)));

    foreach ($ticketList as $ticket) {
        echo sprintf("%s - %s\n", $ticket->id(), $ticket->attribute('last_message_subject') ?? '(no subject)');
    }
    echo "\n";
}
