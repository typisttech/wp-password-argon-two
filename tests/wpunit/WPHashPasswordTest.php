<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class WPHashPasswordTest extends WPTestCase
{
    /** @test */
    public function it_returns_argon2i_hashed_ciphertext()
    {
        $ciphertext = wp_hash_password('testing_it_returns_argon2i_hashed_ciphertext');

        $this->assertFalse(
            password_needs_rehash($ciphertext, PASSWORD_ARGON2I, WP_PASSWORD_ARGON_TWO_OPTIONS)
        );
    }

    /** @test */
    public function its_ciphertext_can_be_checked()
    {
        $password = 'testing_its_ciphertext_can_be_checked';
        $ciphertext = wp_hash_password($password);
        $check = wp_check_password($password, $ciphertext);

        $this->assertTrue($check);
    }
}
