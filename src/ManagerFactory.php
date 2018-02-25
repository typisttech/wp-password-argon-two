<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

class ManagerFactory
{
    private const REQUIRED_CONSTANTS = [
        'WP_PASSWORD_ARGON_TWO_PEPPER',
        'WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS',
        'WP_PASSWORD_ARGON_TWO_OPTIONS',
    ];

    public static function make(): Manager
    {
        self::ensurePHPCompiledWithPasswordArgonTwo();
        self::ensureRequiredConstantsDefined();

        $passwordLock = new PasswordLock(WP_PASSWORD_ARGON_TWO_PEPPER, WP_PASSWORD_ARGON_TWO_OPTIONS);

        $validators = array_map(function (string $pepper): ValidatorInterface {
            return new Validator($pepper);
        }, (array) WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS);

        $validators[] = new WordPressValidator();

        return new Manager($passwordLock, ...$validators);
    }

    private static function ensurePHPCompiledWithPasswordArgonTwo(): void
    {
        if (defined('PASSWORD_ARGON2I')) {
            return;
        }

        wp_die(
            self::phpNotCompiledWithPasswordArgonTwoMessage(),
            self::wpDieTitle()
        );
    }

    private static function phpNotCompiledWithPasswordArgonTwoMessage(): string
    {
        $message = __(
            'WP Password Argon Two requires PHP to be compiled <code>--with-password-argon2</code>.',
            'wp-password-argon-two'
        );

        return sprintf(
            '<p>%1$s</p><p>%2$s</p>',
            wp_kses($message, ['code' => []]),
            self::linkToProjectReadme()
        );
    }

    private static function linkToProjectReadme(): string
    {
        $text = __('Learn more on <a href="%1$s">the project readme</a>.', 'wp-password-argon-two');

        return sprintf(
            wp_kses($text, ['a' => ['href' => []]]),
            'https://github.com/TypistTech/wp-password-argon-two'
        );
    }

    private static function wpDieTitle(): string
    {
        return esc_html__('WP Password Argon Two', 'wp-password-argon-two');
    }

    private static function ensureRequiredConstantsDefined(): void
    {
        $undefinedConstantNames = array_filter(self::REQUIRED_CONSTANTS, function (string $constantName): bool {
            return ! defined($constantName);
        });

        if (empty($undefinedConstantNames)) {
            return;
        }

        wp_die(
            self::missingRequiredConstantMessage($undefinedConstantNames),
            self::wpDieTitle()
        );
    }

    private static function missingRequiredConstantMessage(array $undefinedConstantNames): string
    {
        $header = esc_html__(
            'WP Password Argon Two requires these constants to be defined:',
            'wp-password-argon-two'
        );

        $listItems = array_reduce(
            $undefinedConstantNames,
            function (string $carry, string $undefinedConstantName): string {
                $carry .= '<li><code>' . $undefinedConstantName . '</code></li>';

                return $carry;
            },
            ''
        );

        return sprintf(
            '<p>%1$s</p><ul>%2$s</ul><p>%3$s</p>',
            $header,
            $listItems,
            self::linkToProjectReadme()
        );
    }
}
