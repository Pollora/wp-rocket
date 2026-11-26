<?php

declare(strict_types=1);

namespace Pollora\WpRocket;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

/**
 * Tells WP Rocket what a Pollora project wants from it.
 *
 * In a Pollora project WordPress lives in `vendor` and `public/cms`: WP Rocket must not
 * write its rules into `.htaccess` nor set `WP_CACHE` unless the project asks for it.
 * Both are off by default; `wordpress.wprocket.generate_htaccess` and
 * `wordpress.wprocket.set_cache_constant` turn them on.
 */
final class WpRocketServiceProvider extends ServiceProvider
{
    /**
     * Document the two settings: a project's own `wordpress.wprocket` wins over these defaults.
     */
    public function register(): void
    {
        $config = $this->config();

        $config->set('wordpress.wprocket', array_merge([
            'generate_htaccess' => false,
            'set_cache_constant' => false,
        ], (array) $config->get('wordpress.wprocket', [])));
    }

    public function boot(): void
    {
        $config = $this->config();

        add_filter('rocket_init_cache_dir_generate_htaccess', $config->get('wordpress.wprocket.generate_htaccess') ? static fn (): true => true : static fn (): false => false);
        add_filter('rocket_set_wp_cache_constant', $config->get('wordpress.wprocket.set_cache_constant') ? static fn (): true => true : static fn (): false => false);
    }

    /**
     * The application's configuration repository, which Laravel binds as `config`.
     *
     * @throws BindingResolutionException when `config` is bound to something else
     */
    private function config(): Repository
    {
        $config = $this->app->make('config');

        if (! $config instanceof Repository) {
            throw new BindingResolutionException('The container does not bind a configuration repository as `config`.');
        }

        return $config;
    }
}
