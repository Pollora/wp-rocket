# Pollora WP Rocket

[WP Rocket](https://wp-rocket.me) for [Pollora](https://pollora.dev), the Laravel framework for WordPress: it keeps WP Rocket from writing its rules into `.htaccess` and from setting `WP_CACHE`, unless the project asks for it.

> This used to live in the framework, as `Pollora\ThirdParty\WpRocket`. The old class name still resolves, to this package.

## Why

In a Pollora project WordPress lives in `vendor` and `public/cms`, and the web server's rules belong to the project. WP Rocket, left alone, writes to `.htaccess` and edits the `WP_CACHE` constant on its own. This package tells it not to, and lets you opt in to either.

## Installation

```bash
composer require pollora/wp-rocket
```

Requires PHP 8.3+ and a Pollora project (Laravel 13). The service provider is discovered by Laravel; nothing to register.

## Configuration

Both settings are off by default. Set them in `config/wordpress.php`:

```php
return [
    'wprocket' => [
        'generate_htaccess' => false, // let WP Rocket write its rules into .htaccess
        'set_cache_constant' => false, // let WP Rocket set WP_CACHE
    ],
];
```

| Key | WP Rocket filter | Default |
| --- | --- | --- |
| `generate_htaccess` | `rocket_init_cache_dir_generate_htaccess` | `false` |
| `set_cache_constant` | `rocket_set_wp_cache_constant` | `false` |

## Coming from the framework

| Was | Is |
| --- | --- |
| `Pollora\ThirdParty\WpRocket\WpRocketServiceProvider` | `Pollora\WpRocket\WpRocketServiceProvider` |

The old name keeps working and is removed in the next major release. Install the package: a framework that no longer ships WP Rocket's integration does nothing for it, and WP Rocket goes back to writing `.htaccess` and setting `WP_CACHE` on its own. `php artisan pollora:doctor` warns when WP Rocket is active and this package is missing.

## Development

```bash
composer install
composer qa     # every gate of the Pollora harness
```

## License

MIT, see [LICENSE](LICENSE).
