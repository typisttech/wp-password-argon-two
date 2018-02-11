<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class PasswordLockTest extends WPTestCase
{
    private const DUMMY_PEPPER = 'dummy_pepper';
    private const DUMMY_PASSWORD = 'pa5$word';
    private const MD5_HASH = '5f4dcc3b5aa765d61d8327deb882cf99';
    private const PHPASS_HASH = '$P$BasW5IPx2SEVGbVdiIEzx2VrRb/.eF0';
    private const BCRYPT_HASH = '$2y$10$EkVBmTI0cbPvPdnTYeVk8eIt6qpHk09C8DB5iZwHbYBu5ot2PyAnq';

    /** @test */
    public function it_implements_password_validator_interface()
    {
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);

        $this->assertInstanceOf(ValidatorInterface::class, $passwordLock);
    }

    /** @test */
    public function it_hashes_with_argon2i()
    {
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);

        $ciphertext = $passwordLock->hash(self::DUMMY_PASSWORD);

        $info = password_get_info($ciphertext);
        $this->assertSame(
            'argon2i',
            $info['algoName']
        );
    }

    /** @test */
    public function it_checks_correct_password()
    {
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);
        $ciphertext = $passwordLock->hash(self::DUMMY_PASSWORD);

        $isValid = $passwordLock->isValid(self::DUMMY_PASSWORD, $ciphertext);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function it_checks_incorrect_password()
    {
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);
        $ciphertext = $passwordLock->hash(self::DUMMY_PASSWORD);

        $isValid = $passwordLock->isValid('incorrect password', $ciphertext);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_does_not_truncate_long_password()
    {
        $longPassword = str_repeat('bbcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+', 110);
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);

        $isValid = $passwordLock->isValid(
            substr($longPassword, 0, -1) . 'a',
            $passwordLock->hash($longPassword)
        );

        $this->assertFalse($isValid);
    }

    /** @test */
    public function it_hash_password_into_shorter_than_256_char_string()
    {
        $longPassword = str_repeat('abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+', 110);
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);

        $ciphertext = $passwordLock->hash($longPassword);

        $this->assertLessThan(
            256,
            strlen($ciphertext)
        );
    }

    /** @test */
    public function it_needs_rehash_when_options_changed()
    {
        $oldOptions = [
            'memory_cost' => 1 << 17, // 128 Mb
            'time_cost' => 2,
            'threads' => 1,
        ];
        $newOptions = array_merge($oldOptions, ['time_cost' => 3]);

        $oldPasswordLock = new PasswordLock(self::DUMMY_PEPPER, $oldOptions);
        $newPasswordLock = new PasswordLock(self::DUMMY_PEPPER, $newOptions);

        $ciphertext = $oldPasswordLock->hash(self::DUMMY_PASSWORD);

        $needsRehash = $newPasswordLock->needsRehash($ciphertext);

        $this->assertTrue($needsRehash);
    }

    /** @test */
    public function it_does_not_need_rehash_when_options_unchanged()
    {
        $options = [
            'memory_cost' => 1 << 17, // 128 Mb
            'time_cost' => 2,
            'threads' => 1,
        ];

        $oldPasswordLock = new PasswordLock(self::DUMMY_PEPPER, $options);
        $newPasswordLock = new PasswordLock(self::DUMMY_PEPPER, $options);

        $ciphertext = $oldPasswordLock->hash(self::DUMMY_PASSWORD);

        $needsRehash = $newPasswordLock->needsRehash($ciphertext);

        $this->assertFalse($needsRehash);
    }

    /** @test */
    public function bcrypt_hash_needs_rehash()
    {
        $this->assertNeedsRehash(self::BCRYPT_HASH);
    }

    /** @test */
    public function md5_hash_needs_rehash()
    {
        $this->assertNeedsRehash(self::MD5_HASH);
    }

    /** @test */
    public function phpass_hash_needs_rehash()
    {
        $this->assertNeedsRehash(self::PHPASS_HASH);
    }

    private function assertNeedsRehash(string $ciphertext)
    {
        $passwordLock = new PasswordLock(self::DUMMY_PEPPER, []);

        $needsRehash = $passwordLock->needsRehash($ciphertext);

        $this->assertTrue($needsRehash);
    }
}
