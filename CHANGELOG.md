# Changelog

All notable changes to `exonet-api-php` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased
[Compare v2.3.0 - Unreleased](https://github.com/exonet/exonet-api-php/compare/v2.3.0...master)

## [v2.3.0](https://github.com/exonet/exonet-api-php/releases/tag/v2.3.0) - 2020-08-07
[Compare v2.2.0 - v2.3.0](https://github.com/exonet/exonet-api-php/compare/v2.2.0...v2.3.0)
### Added
- Add the `total()` method to resource sets to get the total number of resources (and not only the number of resources in the current resource set).
- Add `nextPage`, `previousPage`, `firstPage` and `lastPage` methods to the `ApiResourceSet` for easy loading of paginated resource sets.
- Add a `getRecursive` method to the `Request` to get the resource set including recursively the resource sets from the following pages.

## [v2.2.0](https://github.com/exonet/exonet-api-php/releases/tag/v2.2.0) - 2019-11-19
[Compare v2.1.1 - v2.2.0](https://github.com/exonet/exonet-api-php/compare/v2.1.1...v2.2.0)
### Changed
- Extend the `ValidationException` to contain all returned validation errors. See the [docs](./docs/exceptions.md) for more information.

## [v2.1.1](https://github.com/exonet/exonet-api-php/releases/tag/v2.1.1) - 2019-09-19
[Compare v2.1.0 - v2.1.1](https://github.com/exonet/exonet-api-php/compare/v2.1.0...v2.1.1)
### Changed
- `DELETE` requests now return `true` when successful. If something went wrong, an exception is still thrown.

## [v2.1.0](https://github.com/exonet/exonet-api-php/releases/tag/v2.1.0) - 2019-09-06
[Compare v2.1.0 - v2.0.0](https://github.com/exonet/exonet-api-php/compare/v2.0.0...v2.1.0)
### Added
- Support for patching resources and relationships.
- Exceptions thrown by the package are extended with the `status` as the exception code, the `code` as detailed code and an array containing the returned variables.

## [v2.0.0](https://github.com/exonet/exonet-api-php/releases/tag/v2.0.0) - 2019-07-02
[Compare v2.0.0 - v1.0.0](https://github.com/exonet/exonet-api-php/compare/v1.0.0...v2.0.0)
## Breaking
- The Client has been refactored to keep consistency between packages in different programming languages. See the updated documentation and examples.

### Added
- Allow the user to define an API URL.
- Making POST request to create new resources.
- Making DELETE request to remove a resource.

## [v1.0.0](https://github.com/exonet/exonet-api-php/releases/tag/v1.0.0) - 2019-04-29
[Compare v0.2.0 - v1.0.0](https://github.com/exonet/exonet-api-php/compare/v0.2.0...v1.0.0)
### Breaking
- The public property `type` in the `ApiResource` class has been renamed to `resourceType` in order not to conflict with the DNS record resource, which has a `type` attribute.

### Added
- Two examples for DNS zones and records.

## [v0.2.0](https://github.com/exonet/exonet-api-php/releases/tag/v0.2.0) - 2018-07-09
[Compare v0.1.0 - v0.2.0](https://github.com/exonet/exonet-api-php/compare/v0.1.0...v0.2.0)
### Added
- Ready to use examples to get ticket details.
- The ApiResourceSet now supports ArrayAccess.

## [v0.1.0](https://github.com/exonet/exonet-api-php/releases/tag/v0.1.0) - 2018-03-08
### Added
- Initial release.
