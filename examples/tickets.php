<?php

// Run this script using: php examples/tickets.php <YOUR-TOKEN>

use Exonet\Api\Structures\ApiResourceSet;

require __DIR__.'/../vendor/autoload.php';

$authentication = new Exonet\Api\Auth\PersonalAccessToken($argv[1]);

$exonetApi = new Exonet\Api\Client($authentication);

// Get all tickets that the used token can access.
$allTickets = $exonetApi->resource('tickets')->get();

// Get all tickets that are considered 'open' by Exonet  and this token is allowed to access.
$openTickets = $exonetApi->resource('tickets')->filter('open')->get();

renderTickets('ID and subject of all tickets:', $allTickets);
renderTickets('ID and subject of open tickets:', $openTickets);

/**
 * Helper function to render ticket lists.
 *
 * @param string         $description The description of the list.
 * @param ApiResourceSet $ticketList  The resource set containing ticket resources.
 */
function renderTickets(string $description, ApiResourceSet $ticketList) : void
{
    echo sprintf("\n%s (%d)\n%s\n", $description, iterator_count($ticketList), str_repeat('-', strlen($description)));

    foreach ($ticketList as $ticket) {
        echo sprintf("%s - %s\n", $ticket->id, $ticket->last_message_subject ?? '(no subject)');
    }
    echo "\n";
}
