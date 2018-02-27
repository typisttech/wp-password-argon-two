<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use PasswordHash;

class PhpassValidator implements ValidatorInterface
{
    /**
     * For passwords hashed with phpass hasher on recent WordPress versions.
     *
     * @param string $password   Plaintext user's password
     * @param string $ciphertext Hash of the user's password to check against.
     *
     * @return bool False, if the $password does not match the hashed password
     */
    public function isValid(string $password, string $ciphertext): bool
    {
        global $wp_hasher;

        if (empty($wp_hasher)) {
            require_once ABSPATH . WPINC . '/class-phpass.php';
            // By default, use the portable hash from phpass
            $wp_hasher = new PasswordHash(8, true);
        }

        return $wp_hasher->CheckPassword($password, $ciphertext);
    }
}
