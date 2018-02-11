<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use LogicException;

class Manager
{
    /**
     * The main Argon2i password lock.
     *
     * @var PasswordLock
     */
    private $passwordLock;

    /**
     * Fallback password validators.
     *
     * @var ValidatorInterface[]
     */
    private $validators;

    /**
     * Whether the ciphertext needs rehash.
     *
     * @var bool
     */
    private $needsRehash = false;

    /**
     * Manager constructor.
     *
     * @param PasswordLock         $passwordLock
     * @param ValidatorInterface[] $validators
     */
    public function __construct(PasswordLock $passwordLock, ValidatorInterface ...$validators) {
        $this->passwordLock = $passwordLock;
        $this->validators = $validators;
    }

    public static function make(): self
    {
        array_map(function (string $constantName): void {
            if (defined($constantName)) {
                return;
            }

            throw new LogicException('WP Password Argon Two: Required constant `' . $constantName . '` not defined.');
        }, ['WP_PASSWORD_ARGON_TWO_PEPPER', 'WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS','WP_PASSWORD_ARGON_TWO_OPTIONS',]);

        $passwordLock = new PasswordLock(WP_PASSWORD_ARGON_TWO_PEPPER, WP_PASSWORD_ARGON_TWO_OPTIONS);

        $validators = array_map(function(string $pepper): ValidatorInterface {
            return new Validator($pepper);
        }, (array) WP_PASSWORD_ARGON_TWO_FALLBACK_PEPPERS);

        $validators[] = new WordPressValidator();

        return new self($passwordLock, ...$validators);
    }

    public function isValid(string $password, string $ciphertext): bool
    {
        if ($this->passwordLock->isValid($password, $ciphertext)) {
            return true;
        }

        $isValid = array_reduce(
            $this->validators,
            function(bool $carry, ValidatorInterface $validator) use ($password, $ciphertext): bool {
                return $carry || $validator->isValid($password, $ciphertext);
            },
            false
        );

        if ($isValid) {
            $this->needsRehash = true;
        }

        return $isValid;
    }

    public function needsRehash(string $ciphertext): bool
    {
        return $this->needsRehash || $this->passwordLock->needsRehash($ciphertext);
    }

    /**
     * Creates a password hash.
     *
     * @param string $password The user's password in plain text.
     *
     * @return string
     */
    public function hash(string $password): string
    {
        return $this->passwordLock->hash($password);
    }
}
