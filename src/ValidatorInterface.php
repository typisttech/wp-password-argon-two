<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

interface ValidatorInterface
{
    /**
     * Validate user submitted password.
     *
     * @param string $password   The user's password in plain text.
     * @param string $ciphertext Hash of the user's password to check against.
     *
     * @return bool
     */
    public function isValid(string $password, string $ciphertext): bool;
}
