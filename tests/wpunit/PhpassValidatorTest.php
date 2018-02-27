<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class PhpassValidatorTest extends WPTestCase
{
    private const DUMMY_PASSWORD = 'password';
    private const DUMMY_CIPHERTEXT = '$P$BasW5IPx2SEVGbVdiIEzx2VrRb/.eF0';

    /** @test */
    public function it_implements_password_validator_interface()
    {
        $validator = new PhpassValidator();

        $this->assertInstanceOf(ValidatorInterface::class, $validator);
    }

    /** @test */
    public function it_checks_correct_password()
    {
        $validator = new PhpassValidator();

        $isValid = $validator->isValid(self::DUMMY_PASSWORD, self::DUMMY_CIPHERTEXT);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function it_checks_incorrect_password()
    {
        $validator = new PhpassValidator();

        $isValid = $validator->isValid('incorrect password', self::DUMMY_CIPHERTEXT);

        $this->assertFalse($isValid);
    }
}
