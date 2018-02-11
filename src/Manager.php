<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use LogicException;

class Manager
{
    /**
     * @var PasswordLock
     */
    private $passwordLock;
    /**
     * @var ValidatorInterface[]
     */
    private $validators;

    /**
     * Manager constructor.
     *
     * @param PasswordLock         $passwordLock
     * @param ValidatorInterface[] $validators
     */
    public function __construct(
        PasswordLock $passwordLock,
        ValidatorInterface ...$validators
    ) {
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
        }, ['WP_PASSWORD_ARGON_TWO_PEPPER', 'WP_PASSWORD_ARGON_TWO_OPTIONS',]);


        $passwordLock = new PasswordLock(WP_PASSWORD_ARGON_TWO_PEPPER, WP_PASSWORD_ARGON_TWO_OPTIONS);
        $validators = [
            new WordPressValidator(),
        ];

        return new self($passwordLock, ...$validators);
    }

    public function isValid(string $password, string $ciphertext): bool
    {
        return array_reduce(
            $this->getValidators(),
            function(bool $carry, ValidatorInterface $validator) use ($password, $ciphertext): bool {
                return $carry || $validator->isValid($password, $ciphertext);
            },
            false
        );
    }

    /**
     * PasswordValidator getter.
     *
     * @return ValidatorInterface[]
     */
    private function getValidators(): array
    {
        return array_merge(
            [$this->passwordLock],
            $this->validators
        );
    }

    public function needsRehash(string $ciphertext): bool
    {
        return $this->passwordLock->needsRehash($ciphertext);
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
