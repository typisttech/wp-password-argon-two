<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use LogicException;

class PasswordLock
{
    private const HASH_HMAC_ALGO = 'sha512';
    private const PASSWORD_HASH_ALGO = PASSWORD_ARGON2I;

    /**
     * Shared secret key used for generating the HMAC variant of the message digest.
     *
     * @var string
     */
    private $pepper;

    /**
     * Password hash options.
     *
     * @var array
     */
    private $options;

    /**
     * PasswordLock constructor.
     *
     * @param string $pepper  Shared secret key used for generating the HMAC variant of the message digest.
     * @param array  $options Password hash options.
     */
    public function __construct(string $pepper, array $options)
    {
        $this->pepper = $pepper;
        $this->options = $options;
    }

    public static function make(): self
    {
        array_map(function (string $constantName): void {
            if (defined($constantName)) {
                return;
            }

            throw new LogicException('WP Password Argon Two: Required constant `' . $constantName . '` not defined.');
        }, ['WP_PASSWORD_ARGON_TWO_PEPPER', 'WP_PASSWORD_ARGON_TWO_OPTIONS',]);

        return new self(WP_PASSWORD_ARGON_TWO_PEPPER, WP_PASSWORD_ARGON_TWO_OPTIONS);
    }

    /**
     * Validate user submitted password.
     *
     * @param string $password   The user's password in plain text.
     * @param string $ciphertext The double hashed password from database.
     *
     * @return bool
     */
    public function isValid(string $password, string $ciphertext): bool
    {
        return password_verify(
            $this->hmac($password),
            $ciphertext
        );
    }

    /**
     * Checks to see if the supplied hash implements the algorithm and options provided. If not, it is assumed that the
     * hash needs to be rehashed.
     *
     * @param string $ciphertext The hashed password from database.
     *
     * @return bool
     */
    public function needsRehash(string $ciphertext): bool
    {
        return password_needs_rehash($ciphertext, self::PASSWORD_HASH_ALGO, $this->options);
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
        return password_hash(
            $this->hmac($password),
            self::PASSWORD_HASH_ALGO,
            $this->options
        );
    }

    private function hmac(string $password): string
    {
        return hash_hmac(self::HASH_HMAC_ALGO, $password, $this->pepper);
    }
}
