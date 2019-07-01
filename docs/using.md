# Using this package
The base of every request to the API is the `Exonet\Api\Client` class. This class will take care of the used authentication
method and optional logging instance. When instantiating this class no constructor arguments are required, however,
you _can_ set the desired authentication class directly instead of using the `->setAuth()` method.

## Authenticating requests
Requests to the API can not be made if no authentication method is set. Currently, the only valid authentication method
is the "Personal Access Token":

```php
$authentication = new Exonet\Api\Auth\PersonalAccessToken('<YOUR_API_TOKEN>');

// or:

$authentication = new Exonet\Api\Auth\PersonalAccessToken();
$authentication->setToken('<YOUR_API_TOKEN>');
```

After getting the authentication instance, you can use it to initialize the client, or set in in an already initialized
client:

```php
$exonetApi = new Exonet\Api\Client($authentication);

// or:

$exonetApi = new Exonet\Api\Client();
$exonetApi->setAuth($authentication);
```

## Connection settings
By default the client will connect to the production API. you can initialise the client and connect
to the test API by providing an URL as second parameter or use the `setApiUrl` method.

```php
$exonetApi = new Exonet\Api\Client($authentication, Exonet\Api\Client::API_TEST_URL);

// or:

$exonetApi = new Exonet\Api\Client();
$exonetApi->setApiUrl(Exonet\Api\Client::API_TEST_URL);
```

## Logging
This package has support for every [PSR-3 compliant](https://www.php-fig.org/psr/psr-3/) logging package (for example 
[Monolog](https://github.com/Seldaek/monolog)). Client errors and request debug information will be logged.

```php
$exonetApi->setLogger($yourLoggingInstance);
```

---

[Back to the index](index.md) | [Making API calls &raquo;](calls.md)
