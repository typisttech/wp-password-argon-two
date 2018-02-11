<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use PasswordHash;

class WordPressValidator implements ValidatorInterface
{
    /**
     * The original `wp_check_password` of WordPress v4.9.4 without rehashing.
     *
     * @param string $password Plaintext user's password
     * @param string $hash     Hash of the user's password to check against.
     *
     * @return bool False, if the $password does not match the hashed password
     */
    public function isValid(string $password, string $hash): bool
    {
        global $wp_hasher;

        // If the hash is still md5...
        if (strlen($hash) <= 32) {
            return hash_equals($hash, md5($password));
        }

        // If the stored hash is longer than an MD5, presume the
        // new style phpass portable hash.
        if (empty($wp_hasher)) {
            require_once ABSPATH . WPINC . '/class-phpass.php';
            // By default, use the portable hash from phpass
            $wp_hasher = new PasswordHash(8, true);
        }

        return $wp_hasher->CheckPassword($password, $hash);
    }
}
