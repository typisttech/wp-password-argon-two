<?php

declare(strict_types=1);

use TypistTech\WPPasswordArgonTwo\Manager;

/**
 * Checks the plaintext password against the hashed Password.
 *
 * @param string     $password   Plaintext user's password
 * @param string     $ciphertext Hash of the user's password to check against.
 * @param string|int $userId     Optional. User ID.
 *
 * @return bool False, if the $password does not match the hashed password
 */
function wp_check_password(string $password, string $ciphertext, $userId = null): bool
{
    $manager = Manager::make();
    $isValid = $manager->isValid($password, $ciphertext);

    if ($isValid && is_numeric($userId) && $manager->needsRehash($ciphertext)) {
        $ciphertext = wp_set_password($password, (int) $userId);
    }

    return (bool) apply_filters('check_password', $isValid, $password, $ciphertext, $userId);
}

/**
 * Create a hash of a plain text password.
 *
 * @param string $password Plain text user password to hash.
 *
 * @return string The hash string of the password.
 */
function wp_hash_password(string $password): string
{
    $manager = Manager::make();

    return $manager->hash($password);
}

/**
 * Updates the user's password with a newly hashed one.
 * The original `wp_set_password` of WordPress v4.9.4 with returning the new $ciphertext.
 *
 * @param string $password The plaintext new user password.
 * @param int    $userId   User ID.
 *
 * @return string
 */
function wp_set_password(string $password, int $userId): string
{
    global $wpdb;

    $ciphertext = wp_hash_password($password);
    $wpdb->update($wpdb->users, ['user_pass' => $ciphertext, 'user_activation_key' => ''], ['ID' => $userId]);

    wp_cache_delete($userId, 'users');

    return $ciphertext;
}
