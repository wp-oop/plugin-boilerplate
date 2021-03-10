# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD
### Fixed
- Errors in local modules (#17).

### Changed
- Depend on WP stubs instead of WP itself (#17).
- Actually use the stubs in Psalm (#17).

### Added
- Local modules are now scanned by Psalm (#17).
- PHPStorm will now use PHPCS via remote interpreter (#17).

## [0.2.0-alpha1] - 2021-03-10
### Added
- Now supports PHP 8, including newer tool versions (#15).
- Psalm will now report info messages on CI (#15).
- Now using Composer v2.

### Removed
- Dropped support for PHP 7.2 (#15).

### Fixed
- Incompatibility with newer `psr/container` (#15).

## [0.1.0-alpha2] - 2021-04-03
### Added
- Using a caching container to cache services.

## [0.1.0-alpha1] - 2021-01-14
Initial version.
