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

/**
 * Closures that LegacyClassAliases registered on the autoload stack.
 *
 * @return list<Closure>
 */
function legacyAliasLoaders(): array
{
    return array_values(array_filter(
        spl_autoload_functions(),
        static fn ($loader): bool => $loader instanceof Closure
            && (new ReflectionFunction($loader))->getClosureScopeClass()?->getName() === LegacyClassAliases::class,
    ));
}

it('aliases the old provider name from the autoloader it registers, and only that name', function (): void {
    (new ReflectionProperty(LegacyClassAliases::class, 'registered'))->setValue(null, false);

    $before = count(legacyAliasLoaders());
    LegacyClassAliases::register();
    $loaders = legacyAliasLoaders();
    $loader = end($loaders);

    expect(count($loaders))->toBe($before + 1);

    // A second alias of the same name is refused by PHP with a warning: proof that the loader aliased it.
    $warnings = [];
    set_error_handler(static function (int $no, string $message) use (&$warnings): bool {
        $warnings[] = $message;

        return true;
    });

    try {
        $loader(LegacyClassAliases::OLD_PROVIDER);
        $loader(LegacyClassAliases::OLD_PROVIDER);
    } finally {
        restore_error_handler();
    }

    expect(class_exists(LegacyClassAliases::OLD_PROVIDER, false))->toBeTrue()
        ->and($warnings)->not->toBeEmpty();

    $other = 'Pollora\\ThirdParty\\WpRocket\\Other';
    $loader($other);

    expect(class_exists($other, false))->toBeFalse();
});

it('registers the autoloader when its register file is loaded', function (): void {
    (new ReflectionProperty(LegacyClassAliases::class, 'registered'))->setValue(null, false);

    $before = count(legacyAliasLoaders());

    require __DIR__.'/../../src/Compatibility/register.php';

    expect(count(legacyAliasLoaders()))->toBe($before + 1)
        ->and((new ReflectionProperty(LegacyClassAliases::class, 'registered'))->getValue())->toBeTrue();
});
