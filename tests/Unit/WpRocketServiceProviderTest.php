<?php

declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Pollora\WpRocket\WpRocketServiceProvider;

/**
 * @return array{provider: WpRocketServiceProvider, config: Repository}
 */
function wpRocketProvider(array $wordpress): array
{
    $app = new Container;
    $app->instance('config', $config = new Repository(['wordpress' => $wordpress]));

    return ['provider' => new WpRocketServiceProvider($app), 'config' => $config];
}

describe('WpRocketServiceProvider', function (): void {
    beforeEach(function (): void {
        $this->registeredFilters = [];

        Brain\Monkey\Functions\when('add_filter')->alias(function ($hook, $callback): true {
            $this->registeredFilters[$hook] = $callback;

            return true;
        });
    });

    it('registers the htaccess filter as true when the setting is on', function (): void {
        ['provider' => $provider] = wpRocketProvider(['wprocket' => ['generate_htaccess' => true, 'set_cache_constant' => false]]);

        $provider->boot();

        expect($this->registeredFilters['rocket_init_cache_dir_generate_htaccess']())->toBeTrue();
        expect($this->registeredFilters['rocket_set_wp_cache_constant']())->toBeFalse();
    });

    it('registers the cache constant filter as true when the setting is on', function (): void {
        ['provider' => $provider] = wpRocketProvider(['wprocket' => ['generate_htaccess' => false, 'set_cache_constant' => true]]);

        $provider->boot();

        expect($this->registeredFilters['rocket_init_cache_dir_generate_htaccess']())->toBeFalse();
        expect($this->registeredFilters['rocket_set_wp_cache_constant']())->toBeTrue();
    });

    it('defaults to false when the settings are missing', function (): void {
        ['provider' => $provider] = wpRocketProvider(['wprocket' => []]);

        $provider->boot();

        expect($this->registeredFilters['rocket_init_cache_dir_generate_htaccess']())->toBeFalse();
        expect($this->registeredFilters['rocket_set_wp_cache_constant']())->toBeFalse();
    });

    it('defaults to false when the project defines nothing at all', function (): void {
        ['provider' => $provider] = wpRocketProvider([]);

        $provider->boot();

        expect($this->registeredFilters['rocket_init_cache_dir_generate_htaccess']())->toBeFalse();
        expect($this->registeredFilters['rocket_set_wp_cache_constant']())->toBeFalse();
    });

    it('registers exactly two filters', function (): void {
        ['provider' => $provider] = wpRocketProvider(['wprocket' => ['generate_htaccess' => true, 'set_cache_constant' => true]]);

        $provider->boot();

        expect($this->registeredFilters)->toHaveCount(2)
            ->toHaveKeys(['rocket_init_cache_dir_generate_htaccess', 'rocket_set_wp_cache_constant']);
    });

    it('documents both settings as off, and keeps what the project set', function (): void {
        ['provider' => $provider, 'config' => $config] = wpRocketProvider(['wprocket' => ['set_cache_constant' => true]]);

        $provider->register();

        expect($config->get('wordpress.wprocket'))->toBe(['generate_htaccess' => false, 'set_cache_constant' => true]);
    });

    it('is discovered by Laravel', function (): void {
        $composer = json_decode((string) file_get_contents(__DIR__.'/../../composer.json'), true);

        expect($composer['extra']['laravel']['providers'])->toBe([WpRocketServiceProvider::class]);
    });
});
