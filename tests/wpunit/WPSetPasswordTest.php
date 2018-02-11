<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class WPSetPasswordTest extends WPTestCase
{
    /** @test */
    public function it_returns_argon2i_hashed_ciphertext()
    {
        $ciphertext = wp_set_password('password', 999);

        $info = password_get_info($ciphertext);
        $this->assertSame(
            'argon2i',
            $info['algoName']
        );
    }

    /** @test */
    public function it_saves_argon2i_hashed_ciphertext()
    {
        $userId = wp_create_user(
            'testing_it_saves_argon2i_hashed_ciphertext',
            'old_password',
            'testing_it_saves_argon2i_hashed_ciphertext@exmaple.com'
        );

        wp_set_password('new-password', $userId);

        $user = get_user_by('id', $userId);

        $info = password_get_info($user->user_pass);
        $this->assertSame(
            'argon2i',
            $info['algoName']
        );
    }

    /** @test */
    public function its_ciphertext_can_be_checked()
    {
        $userId = wp_create_user(
            'testing_its_ciphertext_can_be_checked',
            'old_password',
            'testing_its_ciphertext_can_be_checked@exmaple.com'
        );

        $password = 'some-password';
        $ciphertext = wp_set_password($password, $userId);
        $check = wp_check_password($password, $ciphertext);

        $this->assertTrue($check);
    }
}
