# Changelog

All notable changes to `pollora/wp-rocket` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- `WpRocketServiceProvider` is now `final` (CONV-08). This is a restriction of the API for anyone who extended it: a class that is not an extension point can no longer be subclassed; compose it instead
- The architecture rules (`tests/Arch.php`) now run as the `Architecture` suite of `phpunit.xml.dist`.
- PHPStan runs at level 10 (was 5). `illuminate/contracts`, which already came with `illuminate/support`, is now a direct requirement. A container that binds `config` to anything else than a configuration repository now throws `BindingResolutionException`.

### Fixed

- Development only: `antecedent/patchwork` is required at commit `d588654` of its `master` branch (antecedent/patchwork#235), aliased as 2.2.6, so that Pest mutants are no longer counted as caught when tests hang. This pin is temporary until the next Patchwork release

## [0.1.0] - 2026-10-28

### Added

- `WpRocketServiceProvider`, moved out of the framework (`Pollora\ThirdParty\WpRocket`): it answers WP Rocket's `rocket_init_cache_dir_generate_htaccess` and `rocket_set_wp_cache_constant` filters from `wordpress.wprocket.generate_htaccess` and `wordpress.wprocket.set_cache_constant`, both off by default
- The two settings now have their defaults registered (`false`), a project's own values winning
- The old class name `Pollora\ThirdParty\WpRocket\WpRocketServiceProvider` still resolves, to the new class; it is removed in the next major release
