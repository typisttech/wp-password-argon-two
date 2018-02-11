<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

interface ValidatorInterface
{
    /**
     * Validate user submitted password.
     *
     * @param string $password   The user's password in plain text.
     * @param string $ciphertext The double hashed password from database.
     *
     * @return bool
     */
    public function isValid(string $password, string $ciphertext): bool;
}
