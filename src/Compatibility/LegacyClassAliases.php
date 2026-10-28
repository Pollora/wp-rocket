<?php

declare(strict_types=1);

namespace Pollora\WpRocket\Compatibility;

use Pollora\WpRocket\WpRocketServiceProvider;

/**
 * Keeps the class name the framework used to ship resolving to the package.
 *
 * `Pollora\ThirdParty\WpRocket\WpRocketServiceProvider` still loads: the first use aliases
 * it to `Pollora\WpRocket\WpRocketServiceProvider`. The old name goes away in the next
 * major release.
 */
final class LegacyClassAliases
{
    public const string OLD_PROVIDER = 'Pollora\\ThirdParty\\WpRocket\\WpRocketServiceProvider';

    private static bool $registered = false;

    public static function register(): void
    {
        if (self::$registered) {
            return;
        }

        self::$registered = true;

        spl_autoload_register(static function (string $class): void {
            if ($class === self::OLD_PROVIDER) {
                class_alias(WpRocketServiceProvider::class, $class);
            }
        });
    }
}
