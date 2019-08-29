# Changelog

All notable changes to `exonet-api-php` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased
[Compare v2.0.0 - Unreleased](https://github.com/exonet/exonet-api-php/compare/v2.0.0...master)
### Breaking
- The `Api` prefix has been removed from the following classes:
  - `ApiResourceSet` --> `ResourceSet`
  - `ApiResource` --> `Resource`
  - `ApiResourceIdentifier` --> `ResourceIdentifier`

### Added
- Support for patching resources and relationships.
- Exceptions thrown by the package are extended with the `status` as the exception code, the `code` as detailed code and an array containing the returned variables.

## [v2.0.0](https://github.com/exonet/exonet-api-php/releases/tag/v2.0.0) - 2019-07-02
[Compare v2.0.0 - v1.0.0](https://github.com/exonet/backend/compare/v1.0.0...v2.0.0)
## Breaking
- The Client has been refactored to keep consistency between packages in different programming languages. See the updated documentation and examples.

### Added
- Allow the user to define an API URL.
- Making POST request to create new resources.
- Making DELETE request to remove a resource.

## [v1.0.0](https://github.com/exonet/exonet-api-php/releases/tag/v1.0.0) - 2019-04-29
[Compare v0.2.0 - v1.0.0](https://github.com/exonet/backend/compare/v0.2.0...v1.0.0)
### Breaking
- The public property `type` in the `Resource` class has been renamed to `resourceType` in order not to conflict with the DNS record resource, which has a `type` attribute.

### Added
- Two examples for DNS zones and records.

## [v0.2.0](https://github.com/exonet/exonet-api-php/releases/tag/v0.2.0) - 2018-07-09
[Compare v0.1.0 - v0.2.0](https://github.com/exonet/backend/compare/v0.1.0...v0.2.0)
### Added
- Ready to use examples to get ticket details.
- The ResourceSet now supports ArrayAccess.

## [v0.1.0](https://github.com/exonet/exonet-api-php/releases/tag/v0.1.0) - 2018-03-08
### Added
- Initial release.
