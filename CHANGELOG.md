# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).


## [[*next-version*]] - YYYY-MM-DD
### Changed
- Renamed `modules.local` to simply `modules` (#28).
- Now using [`dhii/services`][] to declare services (#31).
- Reworked modules (#32):
  * No more `inc`: sources live in `src`.
  * No more `module.php`: simply autoload them.
  * Main module: `modules` entirely optional.
  * Reworked bootstrap: now more re-usable.
  * Reworked tests: now with base classes for different "levels".

### Fixed
- Missing WP plugin API functions (#32).

### Added
- WordPress directory mapping: easier debugging, and more IDE features (#32).
- Modules added to tools: tests being run, code being scanned (#32).
- Modular build script: build the plugin, including modules, in place (#32).
- Application init test (#32).
- Node and WP-CLI now parts of relevant services (#32).

### Added
- Now building on PHP 8.1 and 8.2 as well (#30).

## [0.2.0] - 2023-08-04
### Fixed
- Maintenance: update old configuration and deps, ran checks.
- Psalm errors.

## [0.2.0-alpha4] - 2022-05-06
### Fixed
- Used to be unable to build due to missing Compose argument mapping (eae9282).
- Used to describe broken method of starting a new repo with this project.

## [0.2.0-alpha3] - 2021-06-17
### Fixed
- Initial install of Composer dependencies fails (#24).
- Deprecated attribute in Psalm config causes failure (#24).

### Added
- PHPStorm integrations for PHPCS and Psalm (#24).

## [0.2.0-alpha2] - 2021-03-10
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


[`dhii/services`]: https://github.com/Dhii/services
