<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class WPCheckPasswordTest extends WPTestCase
{
    private const DUMMY_PASSWORD = 'password';

    // Pepper is defined in TypistTech\WPPasswordArgonTwo\Helper\Wpunit.
    private const ARGON_TWO_HASH = '$argon2i$v=19$m=1024,t=2,p=2$NHE3Vm5aeE8vRExBcVpieA$hf23XqOpqT403Ya0U+Bd+4JhYlMAgNEvFx/CisPkrX4';
    // Fallback pepper is 'my-second-pepper';
    private const ARGON_TWO_FALLBACK_PEPPER_HASH = '$argon2i$v=19$m=1024,t=2,p=2$TUFxYm5XSkJ1b29YLmI5Mg$qn5gHvOEVi1Ixenu7Uax8VWMwu5JW6mM0Ob/kJBwB2A';
    private const BCRYPT_HASH = '$2y$10$EkVBmTI0cbPvPdnTYeVk8eIt6qpHk09C8DB5iZwHbYBu5ot2PyAnq';
    private const MD5_HASH = '5f4dcc3b5aa765d61d8327deb882cf99';
    private const PHPASS_HASH = '$P$BasW5IPx2SEVGbVdiIEzx2VrRb/.eF0';

    /** @test */
    public function it_checks_correct_argon2_hash()
    {
        $this->assertCorrectPassword('argon2user', self::DUMMY_PASSWORD, self::ARGON_TWO_HASH);
    }

    /** @test */
    public function it_checks_incorrect_argon2_hash()
    {
        $this->assertIncorrectPassword('argon2user', 'incorrectPassword', self::ARGON_TWO_HASH);
    }

    /** @test */
    public function it_does_not_rehash_argon2_hash()
    {
        $this->assertRehashToArgon2i('argon2user', self::DUMMY_PASSWORD, self::ARGON_TWO_HASH);
        $user = get_user_by('login', 'argon2user');
        $this->assertSame(
            self::ARGON_TWO_HASH,
            $user->user_pass
        );
    }

    /** @test */
    public function it_checks_correct_argon2_fallback_pepper_hash()
    {
        $this->assertCorrectPassword('argon2fallbackpepperuser', self::DUMMY_PASSWORD, self::ARGON_TWO_FALLBACK_PEPPER_HASH);
    }

    /** @test */
    public function it_checks_incorrect_argon2_fallback_pepper_hash()
    {
        $this->assertIncorrectPassword('argon2fallbackpepperuser', 'incorrectPassword', self::ARGON_TWO_FALLBACK_PEPPER_HASH);
    }

    /** @test */
    public function it_rehash_argon2_fallback_pepper_hash()
    {
        $this->assertRehashToArgon2i('argon2fallbackpepperuser', self::DUMMY_PASSWORD, self::ARGON_TWO_FALLBACK_PEPPER_HASH);
        $user = get_user_by('login', 'argon2fallbackpepperuser');
        $this->assertNotSame(
            self::ARGON_TWO_FALLBACK_PEPPER_HASH,
            $user->user_pass
        );
    }

    /** @test */
    public function it_checks_correct_bcrypt_hash()
    {
        $this->assertCorrectPassword('bcryptuser', self::DUMMY_PASSWORD, self::BCRYPT_HASH);
    }

    /** @test */
    public function it_checks_incorrect_bcrypt_hash()
    {
        $this->assertIncorrectPassword('bcryptuser', 'incorrectPassword', self::BCRYPT_HASH);
    }

    /** @test */
    public function it_rehash_bcrypt_hash()
    {
        $this->assertRehashToArgon2i('bcryptuser', self::DUMMY_PASSWORD, self::BCRYPT_HASH);
    }

    /** @test */
    public function it_checks_correct_md5_hash()
    {
        $this->assertCorrectPassword('md5user', self::DUMMY_PASSWORD, self::MD5_HASH);
    }

    /** @test */
    public function it_checks_incorrect_md5_hash()
    {
        $this->assertIncorrectPassword('md5user', 'incorrectPassword', self::MD5_HASH);
    }

    /** @test */
    public function it_rehash_md5_hash()
    {
        $this->assertRehashToArgon2i('md5user', self::DUMMY_PASSWORD, self::MD5_HASH);
    }

    /** @test */
    public function it_checks_correct_phpass_hash()
    {
        $this->assertCorrectPassword('phpassuser', self::DUMMY_PASSWORD, self::PHPASS_HASH);
    }

    /** @test */
    public function it_checks_incorrect_phpass_hash()
    {
        $this->assertIncorrectPassword('phpassuser', 'incorrectPassword', self::PHPASS_HASH);
    }

    /** @test */
    public function it_rehash_phpass_hash()
    {
        $this->assertRehashToArgon2i('phpassuser', self::DUMMY_PASSWORD, self::PHPASS_HASH);
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
            'user_login'      => $login,
            'user_pass'       => $ciphertext,
            'user_nicename'   => $login,
            'user_email'      => $login.'@wp.dev',
            'user_registered' => '2018-01-01 00:00:00',
            'display_name'    => $login,
        ]);
    }
}
