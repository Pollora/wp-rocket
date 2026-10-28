# Changelog

All notable changes to `pollora/wp-rocket` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0] - 2026-10-28

### Added

- `WpRocketServiceProvider`, moved out of the framework (`Pollora\ThirdParty\WpRocket`): it answers WP Rocket's `rocket_init_cache_dir_generate_htaccess` and `rocket_set_wp_cache_constant` filters from `wordpress.wprocket.generate_htaccess` and `wordpress.wprocket.set_cache_constant`, both off by default
- The two settings now have their defaults registered (`false`), a project's own values winning
- The old class name `Pollora\ThirdParty\WpRocket\WpRocketServiceProvider` still resolves, to the new class; it is removed in the next major release
