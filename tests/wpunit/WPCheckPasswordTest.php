<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class WPCheckPasswordTest extends WPTestCase
{
    private const MD5_HASH = '5f4dcc3b5aa765d61d8327deb882cf99';
    private const PHPASS_HASH = '$P$BasW5IPx2SEVGbVdiIEzx2VrRb/.eF0';
    private const BCRYPT_HASH = '$2y$10$EkVBmTI0cbPvPdnTYeVk8eIt6qpHk09C8DB5iZwHbYBu5ot2PyAnq';

    /** @test */
    public function it_checks_correct_bcrypt_hash()
    {
        $this->assertCorrectPassword('bcryptuser', 'password', self::BCRYPT_HASH);
    }

    /** @test */
    public function it_checks_incorrect_bcrypt_hash()
    {
        $this->assertIncorrectPassword('bcryptuser', 'incorrectPassword', self::BCRYPT_HASH);
    }

    /** @test */
    public function it_rehash_bcrypt_hash()
    {
        $this->assertRehashToArgon2i('bcryptuser', 'password', self::BCRYPT_HASH);
    }

    /** @test */
    public function it_checks_correct_md5_hash()
    {
        $this->assertCorrectPassword('md5user', 'password', self::MD5_HASH);
    }

    /** @test */
    public function it_checks_incorrect_md5_hash()
    {
        $this->assertIncorrectPassword('md5user', 'incorrectPassword', self::MD5_HASH);
    }

    /** @test */
    public function it_rehash_md5_hash()
    {
        $this->assertRehashToArgon2i('md5user', 'password', self::MD5_HASH);
    }

    /** @test */
    public function it_checks_correct_phpass_hash()
    {
        $this->assertCorrectPassword('phpassuser', 'password', self::PHPASS_HASH);
    }

    /** @test */
    public function it_checks_incorrect_phpass_hash()
    {
        $this->assertIncorrectPassword('phpassuser', 'incorrectPassword', self::PHPASS_HASH);
    }

    /** @test */
    public function it_rehash_phpass_hash()
    {
        $this->assertRehashToArgon2i('phpassuser', 'password', self::PHPASS_HASH);
    }

    private function assertCorrectPassword(string $login, string $password, string $ciphertext)
    {
        $this->haveUserInDatabase($login, $ciphertext);

        $isValid = wp_check_password($password, $ciphertext);

        $this->assertTrue($isValid);
    }

    private function assertIncorrectPassword(string $login, string $incorrectPassword, string $ciphertext)
    {
        $this->haveUserInDatabase($login, $ciphertext);

        $isValid = wp_check_password($incorrectPassword, $ciphertext);

        $this->assertFalse($isValid);
    }

    private function assertRehashToArgon2i(string $login, string $password, string $ciphertext)
    {
        $this->haveUserInDatabase($login, $ciphertext);

        $user = get_user_by('login', $login);

        wp_check_password($password, $ciphertext, $user->ID);

        $this->assertPasswordArgon2iHashed($login);
    }

    private function assertPasswordArgon2iHashed(string $login)
    {
        $user = get_user_by('login', $login);
        $info = password_get_info($user->user_pass);
        $this->assertSame(
            'argon2i',
            $info['algoName']
        );
    }

    private function haveUserInDatabase(string $login, string $ciphertext)
    {
        $this->tester->haveOrUpdateInDatabase('wp_users', [
            'user_login' => $login,
            'user_pass' => $ciphertext,
            'user_nicename' => $login,
            'user_email' => $login . '@wp.dev',
            'user_registered' => '2018-01-01 00:00:00',
            'display_name' => $login,
        ]);
    }
}
