<?php

declare(strict_types=1);

use Pollora\WpRocket\Compatibility\LegacyClassAliases;
use Pollora\WpRocket\WpRocketServiceProvider;

it('resolves the name the framework used to ship to the package provider', function (): void {
    expect(class_exists(LegacyClassAliases::OLD_PROVIDER))->toBeTrue()
        ->and(is_a(LegacyClassAliases::OLD_PROVIDER, WpRocketServiceProvider::class, true))->toBeTrue();
});
