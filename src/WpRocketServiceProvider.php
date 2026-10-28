<?php

declare(strict_types=1);

namespace Pollora\WpRocket;

use Illuminate\Support\ServiceProvider;

/**
 * Tells WP Rocket what a Pollora project wants from it.
 *
 * In a Pollora project WordPress lives in `vendor` and `public/cms`: WP Rocket must not
 * write its rules into `.htaccess` nor set `WP_CACHE` unless the project asks for it.
 * Both are off by default; `wordpress.wprocket.generate_htaccess` and
 * `wordpress.wprocket.set_cache_constant` turn them on.
 */
class WpRocketServiceProvider extends ServiceProvider
{
    /**
     * Document the two settings: a project's own `wordpress.wprocket` wins over these defaults.
     */
    public function register(): void
    {
        $config = $this->app->make('config');

        $config->set('wordpress.wprocket', array_merge([
            'generate_htaccess' => false,
            'set_cache_constant' => false,
        ], (array) $config->get('wordpress.wprocket', [])));
    }

    public function boot(): void
    {
        $config = $this->app->make('config')->get('wordpress.wprocket', []);

        add_filter('rocket_init_cache_dir_generate_htaccess', ($config['generate_htaccess'] ?? false) ? static fn (): true => true : static fn (): false => false);
        add_filter('rocket_set_wp_cache_constant', ($config['set_cache_constant'] ?? false) ? static fn (): true => true : static fn (): false => false);
    }
}
