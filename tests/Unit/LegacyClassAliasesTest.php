<?php

declare(strict_types=1);

use Pollora\WpRocket\Compatibility\LegacyClassAliases;
use Pollora\WpRocket\WpRocketServiceProvider;

it('resolves the name the framework used to ship to the package provider', function (): void {
    expect(class_exists(LegacyClassAliases::OLD_PROVIDER))->toBeTrue()
        ->and(is_a(LegacyClassAliases::OLD_PROVIDER, WpRocketServiceProvider::class, true))->toBeTrue();
});

it('registers its autoloader once, then ignores further calls', function (): void {
    $count = fn (): int => count(array_filter(
        spl_autoload_functions(),
        static fn ($loader): bool => $loader instanceof Closure
            && (new ReflectionFunction($loader))->getClosureScopeClass()?->getName() === LegacyClassAliases::class,
    ));

    // The composer `files` entry already registered it before the test: start from a fresh state.
    $flag = new ReflectionProperty(LegacyClassAliases::class, 'registered');
    $flag->setValue(null, false);

    $before = $count();

    LegacyClassAliases::register();
    $afterFirst = $count();
    LegacyClassAliases::register();

    expect($afterFirst)->toBe($before + 1)
        ->and($count())->toBe($afterFirst)
        ->and($flag->getValue())->toBeTrue();
});

it('leaves other class names alone', function (): void {
    expect(class_exists('Pollora\\ThirdParty\\WpRocket\\SomethingElse'))->toBeFalse();
});
