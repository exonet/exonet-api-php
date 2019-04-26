# Changelog

All notable changes to `exonet-api-php` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased
[Compare v0.2.0 - Unreleased](https://github.com/exonet/exonet-api-php/compare/v0.2.0...master)
### Breaking
- The public property `type` in the `ApiResource` class has been renamed to `resourceType` in order not to conflict with the DNS record resource, which has a `type` attribute.

### Added
- Two examples for DNS zones and records.

## [v0.2.0](https://github.com/exonet/exonet-api-php/releases/tag/v0.2.0) - 2018-07-09
[Compare v0.1.0 - v0.2.0](https://github.com/exonet/backend/compare/v0.1.0...v0.2.0)
### Added
- Ready to use examples to get ticket details.
- The ApiResourceSet now supports ArrayAccess.

## [v0.1.0](https://github.com/exonet/exonet-api-php/releases/tag/v0.1.0) - 2018-03-08
### Added
- Initial release.
