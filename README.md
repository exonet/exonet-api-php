# exonet-api-php

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

The Exonet API Client allows easy usage of the Exonet API.

## Install

Via Composer

``` bash
$ composer require exonet/exonet-api-php
```

## Usage

``` php
require 'vendor/autoload.php';

$authentication = new Exonet\Api\Auth\PersonalAccessToken('<YOUR_API_TOKEN>');

$exonetApi = new Exonet\Api\Client();
$exonetApi->setAuth($authentication); // Or: $exonetApi = new Exonet\Api\Client($authentication);

$certificates = $exonetApi->resource('certificates')->get();
```

Please see the `/docs` folder for complete documentation and additional examples.

## Examples

The `/examples` directory contains ready to use scripts to help you get started. These examples can be executed with your personal access token. One of them gets a ticket with it's emails and prints the details.

```bash
$ php examples/ticket_details.php <YOUR-TOKEN>
```

This should make two API calls and print the ticket and email details for one of your tickets.

## Change log

Please see [releases](link-releases) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](.github/CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues please email [development@exonet.nl](mailto:development@exonet.nl) instead
of using the issue tracker.

## Credits

- [Exonet][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/exonet/exonet-api-php.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/exonet/exonet-api-php/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/exonet/exonet-api-php.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/exonet/exonet-api-php
[link-travis]: https://travis-ci.org/exonet/exonet-api-php
[link-downloads]: https://packagist.org/packages/exonet/exonet-api-php
[link-author]: https://github.com/exonet
[link-contributors]: ../../contributors
