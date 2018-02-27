<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

class MDFiveValidator implements ValidatorInterface
{
    /**
     * For passwords hashed with MD5 on very old WordPress versions.
     *
     * @param string $password   Plaintext user's password
     * @param string $ciphertext Hash of the user's password to check against.
     *
     * @return bool False, if the $password does not match the hashed password
     */
    public function isValid(string $password, string $ciphertext): bool
    {
        return hash_equals(
            $ciphertext,
            md5($password)
        );
    }
}
