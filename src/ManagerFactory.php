<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use LogicException;

class ManagerFactory
{
    public static function make(): Manager
    {
        array_map(function (string $constantName): void {
            if (defined($constantName)) {
                return;
            }

            throw new LogicException('WP Password Argon Two: Required constant `' . $constantName . '` not defined.');
        }, ['WP_PASSWORD_ARGON_TWO_PEPPER', 'WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS', 'WP_PASSWORD_ARGON_TWO_OPTIONS']);

        $passwordLock = new PasswordLock(WP_PASSWORD_ARGON_TWO_PEPPER, WP_PASSWORD_ARGON_TWO_OPTIONS);

        $validators = array_map(function (string $pepper): ValidatorInterface {
            return new Validator($pepper);
        }, (array) WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS);

        $validators[] = new WordPressValidator();

        return new Manager($passwordLock, ...$validators);
    }
}
