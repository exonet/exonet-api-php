# Resources

## Getting data from a resource
Get data from the attributes or relationships of a resource. See [making API calls](calls.md) for more information on 
how to get resources from the API.

```php
$dnsRecord = $client->resource('dns_records')->get('VX09kwR3KxNo');

// Show an attribute value:
echo $dnsRecord->attribute('name');

// Get a related value, in this case the name of the DNS zone:
echo $dnsRecord->related('zone')->get()->attribute('name');
```

## Creating a new resource
Post a new resource to the API by setting its attributes and relationships:

```php
$record = new Resource('dns_records');
$record->attribute('name', 'www');
$record->attribute('type', 'A');
$record->attribute('content', '192.168.1.100');
$record->attribute('ttl', 3600);

// The value of a relationship must be defined as a resource identifier.
$record->relationship('zone', new ResourceIdentifier('dns_zones', 'VX09kwR3KxNo'));
$result = $record->post();

print_r($result);
```

## Modifying a resource
Modify a resource by changing its attributes and/or relationships:

```php
$dnsRecord = $client->resource('dns_records')->get('VX09kwR3KxNo');
// Or, if there is no need to retrieve the resource from the API first you can use the following:
// $dnsRecord = new Resource('dns_records', 'VX09kwR3KxNo');

// Change the 'name' attribute to 'changed-name'.
$dnsRecord->attribute('name', 'changed-name');

// The value of a relationship must be defined as a resource identifier.
$dnsRecord->relationship('dns_zone', new ResourceIdentifier('dns_zones', 'X09kwRdbbAxN'));

// Patch the changed data to the API.
$dnsRecord->patch();
``` 

## Deleting a resource
Delete a resource with a given ID:

```php
$dnsRecord = $client->resource('dns_records')->get('VX09kwR3KxNo');
// Or, if there is no need to retrieve the resource from the API first you can use the following:
// $dnsRecord = new Resource('dns_records', 'VX09kwR3KxNo');

$dnsRecord->delete();
```
