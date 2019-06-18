# Making API calls
After the client has been initialised, use the `resource` method to define which type of resource you want to get from 
the API:

```php
$certificatesRequest = $client->resource('certificates');
```

This will return a `Request` instance on which additional request parameters can be set:

```php
// Define which filters must be used:
$certificatesRequest->filter('expired', true); // 'true' is default and can be omitted.

// Set the number of resources to get:
$certificatesRequest->size(10);

// Set the page to get:
$certificatesRequest->page(2);
```

After setting the parameters you can call the `->get()` method to retrieve the resources:
```php
$certificates = $certificatesRequest->get();
```

## Getting a single resource by ID
If you want to get a specific resource by its ID, use the `id()` method to define specify the ID of the resource:
```php
$certificate = $client->resource('certificates')->id('VX09kwR3KxNo')->get();
```

---

[&laquo; Using this package](using.md) | [Back to the index](index.md) | [Using API Responses &raquo;](api_responses.md)
